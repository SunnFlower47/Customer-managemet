<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get revenue report
     */
    public function revenue(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'bulan' => 'nullable|integer|min:1|max:12',
                'tahun' => 'nullable|integer|min:2020|max:2030',
                'penagih_id' => 'nullable|exists:penagihs,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Pembayaran::with(['pelanggan', 'pelanggan.paket', 'penagih']);

            // Apply filters
            if ($request->has('start_date') && $request->start_date) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
            }

            if ($request->has('bulan') && $request->bulan) {
                $query->where('bulan_tagihan', $request->bulan);
            }

            if ($request->has('tahun') && $request->tahun) {
                $query->where('tahun_tagihan', $request->tahun);
            }

            if ($request->has('penagih_id') && $request->penagih_id) {
                $query->where('penagih_id', $request->penagih_id);
            }

            // Get paid and unpaid payments
            $paidPayments = clone $query;
            $unpaidPayments = clone $query;

            $paidData = $paidPayments->where('status', 'lunas')->get();
            $unpaidData = $unpaidPayments->where('status', 'belum_lunas')->get();

            // Calculate totals
            $totalRevenue = $paidData->sum('harga_paket');
            $totalUnpaid = $unpaidData->sum('harga_paket');
            $totalPayments = $paidData->count() + $unpaidData->count();
            $paidCount = $paidData->count();
            $unpaidCount = $unpaidData->count();

            // Group by package
            $packageStats = $paidData->groupBy('paket_id')->map(function ($payments, $paketId) {
                $firstPayment = $payments->first();
                return [
                    'paket_id' => $paketId,
                    'nama_paket' => $firstPayment->nama_paket ?? 'Unknown',
                    'harga_paket' => $firstPayment->harga_paket ?? 0,
                    'total_revenue' => $payments->sum('harga_paket'),
                    'payment_count' => $payments->count()
                ];
            })->values();

            // Group by collector
            $collectorStats = $paidData->groupBy('penagih_id')->map(function ($payments, $penagihId) {
                $firstPayment = $payments->first();
                return [
                    'penagih_id' => $penagihId,
                    'nama_penagih' => $firstPayment->penagih->nama ?? 'Tidak ada penagih',
                    'total_revenue' => $payments->sum('harga_paket'),
                    'payment_count' => $payments->count()
                ];
            })->values();

            // Monthly breakdown
            $monthlyBreakdown = $paidData->groupBy(function ($payment) {
                return $payment->created_at->format('Y-m');
            })->map(function ($payments, $month) {
                return [
                    'month' => $month,
                    'month_name' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                    'total_revenue' => $payments->sum('harga_paket'),
                    'payment_count' => $payments->count()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Laporan pendapatan berhasil diambil',
                'data' => [
                    'summary' => [
                        'total_revenue' => $totalRevenue,
                        'total_unpaid' => $totalUnpaid,
                        'total_payments' => $totalPayments,
                        'paid_count' => $paidCount,
                        'unpaid_count' => $unpaidCount,
                        'payment_rate' => $totalPayments > 0 ? round(($paidCount / $totalPayments) * 100, 2) : 0
                    ],
                    'package_statistics' => $packageStats,
                    'collector_statistics' => $collectorStats,
                    'monthly_breakdown' => $monthlyBreakdown,
                    'paid_payments' => $paidData->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'kode_pembayaran' => $payment->kode_pembayaran,
                            'nama_pelanggan' => $payment->pelanggan->nama,
                            'pppoe' => $payment->pelanggan->pppoe,
                            'nama_paket' => $payment->nama_paket,
                            'harga_paket' => $payment->harga_paket,
                            'tanggal_bayar' => $payment->tanggal_bayar ? $payment->tanggal_bayar->format('d/m/Y H:i') : null,
                            'periode' => $payment->bulan_tagihan . '/' . $payment->tahun_tagihan,
                            'penagih_nama' => $payment->penagih->nama ?? null
                        ];
                    }),
                    'unpaid_payments' => $unpaidData->map(function ($payment) {
                        $dueDate = $payment->created_at->addMonth();
                        $isOverdue = now()->isAfter($dueDate);
                        return [
                            'id' => $payment->id,
                            'kode_pembayaran' => $payment->kode_pembayaran,
                            'nama_pelanggan' => $payment->pelanggan->nama,
                            'pppoe' => $payment->pelanggan->pppoe,
                            'nama_paket' => $payment->nama_paket,
                            'harga_paket' => $payment->harga_paket,
                            'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                            'is_overdue' => $isOverdue,
                            'days_overdue' => $isOverdue ? now()->diffInDays($dueDate) : 0,
                            'periode' => $payment->bulan_tagihan . '/' . $payment->tahun_tagihan,
                            'penagih_nama' => $payment->penagih->nama ?? null
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get expense report
     */
    public function expenses(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'kategori' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Pengeluaran::query();

            // Apply filters
            if ($request->has('start_date') && $request->start_date) {
                $query->where('tanggal', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->where('tanggal', '<=', $request->end_date);
            }

            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', 'like', "%{$request->kategori}%");
            }

            $expenses = $query->orderBy('tanggal', 'desc')->get();

            // Calculate totals
            $totalExpenses = $expenses->sum('jumlah');
            $expenseCount = $expenses->count();

            // Group by category
            $categoryStats = $expenses->groupBy('kategori')->map(function ($expenses, $kategori) {
                return [
                    'kategori' => $kategori,
                    'total_amount' => $expenses->sum('jumlah'),
                    'expense_count' => $expenses->count(),
                    'average_amount' => $expenses->avg('jumlah')
                ];
            })->values();

            // Monthly breakdown
            $monthlyBreakdown = $expenses->groupBy(function ($expense) {
                return $expense->tanggal->format('Y-m');
            })->map(function ($expenses, $month) {
                return [
                    'month' => $month,
                    'month_name' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                    'total_amount' => $expenses->sum('jumlah'),
                    'expense_count' => $expenses->count()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Laporan pengeluaran berhasil diambil',
                'data' => [
                    'summary' => [
                        'total_expenses' => $totalExpenses,
                        'expense_count' => $expenseCount,
                        'average_expense' => $expenseCount > 0 ? round($totalExpenses / $expenseCount, 2) : 0
                    ],
                    'category_statistics' => $categoryStats,
                    'monthly_breakdown' => $monthlyBreakdown,
                    'expenses' => $expenses->map(function ($expense) {
                        return [
                            'id' => $expense->id,
                            'kategori' => $expense->kategori,
                            'deskripsi' => $expense->deskripsi,
                            'jumlah' => $expense->jumlah,
                            'tanggal' => $expense->tanggal->format('d/m/Y'),
                            'created_at' => $expense->created_at->format('d/m/Y H:i')
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get profit/loss report
     */
    public function profitLoss(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'bulan' => 'nullable|integer|min:1|max:12',
                'tahun' => 'nullable|integer|min:2020|max:2030'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get revenue
            $revenueQuery = Pembayaran::where('status', 'lunas');
            $expenseQuery = Pengeluaran::query();

            // Apply date filters
            if ($request->has('start_date') && $request->start_date) {
                $revenueQuery->where('tanggal_bayar', '>=', $request->start_date);
                $expenseQuery->where('tanggal', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $revenueQuery->where('tanggal_bayar', '<=', $request->end_date . ' 23:59:59');
                $expenseQuery->where('tanggal', '<=', $request->end_date);
            }

            if ($request->has('bulan') && $request->bulan) {
                $revenueQuery->where('bulan_tagihan', $request->bulan);
            }

            if ($request->has('tahun') && $request->tahun) {
                $revenueQuery->where('tahun_tagihan', $request->tahun);
            }

            $totalRevenue = $revenueQuery->sum('harga_paket');
            $totalExpenses = $expenseQuery->sum('jumlah');
            $netProfit = $totalRevenue - $totalExpenses;

            // Monthly breakdown
            $monthlyRevenue = Pembayaran::where('status', 'lunas')
                ->where('tanggal_bayar', '>=', now()->subMonths(12))
                ->select(
                    DB::raw('MONTH(tanggal_bayar) as month'),
                    DB::raw('YEAR(tanggal_bayar) as year'),
                    DB::raw('SUM(harga_paket) as revenue')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            $monthlyExpenses = Pengeluaran::where('tanggal', '>=', now()->subMonths(12))
                ->select(
                    DB::raw('MONTH(tanggal) as month'),
                    DB::raw('YEAR(tanggal) as year'),
                    DB::raw('SUM(jumlah) as expenses')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            // Combine monthly data
            $monthlyData = collect();
            for ($i = 0; $i < 12; $i++) {
                $date = now()->subMonths(11 - $i);
                $month = $date->month;
                $year = $date->year;

                $revenue = $monthlyRevenue->where('month', $month)->where('year', $year)->first();
                $expense = $monthlyExpenses->where('month', $month)->where('year', $year)->first();

                $monthlyData->push([
                    'month' => $month,
                    'year' => $year,
                    'month_name' => $date->format('M Y'),
                    'revenue' => $revenue ? $revenue->revenue : 0,
                    'expenses' => $expense ? $expense->expenses : 0,
                    'profit' => ($revenue ? $revenue->revenue : 0) - ($expense ? $expense->expenses : 0)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Laporan laba rugi berhasil diambil',
                'data' => [
                    'summary' => [
                        'total_revenue' => $totalRevenue,
                        'total_expenses' => $totalExpenses,
                        'net_profit' => $netProfit,
                        'profit_margin' => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0
                    ],
                    'monthly_breakdown' => $monthlyData
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
