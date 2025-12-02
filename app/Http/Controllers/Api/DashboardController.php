<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Paket;
use App\Models\Penagih;
use App\Models\Pengeluaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            // Basic counts
            $totalCustomers = Pelanggan::count();
            // Include customers with status 'aktif' or 'bayar double' (both are active)
            $activeCustomers = Pelanggan::whereIn('status', ['aktif', 'bayar double'])->count();
            $totalPackages = Paket::count();
            $activePackages = Paket::where('aktif', true)->count();
            $totalCollectors = Penagih::count();

            // Payment statistics
            $totalPayments = Pembayaran::count();
            $paidPayments = Pembayaran::where('status', 'lunas')->count();
            $unpaidPayments = Pembayaran::where('status', 'belum_lunas')->count();

            // Revenue statistics
            $totalRevenue = Pembayaran::where('status', 'lunas')->sum('harga_paket');
            $unpaidAmount = Pembayaran::where('status', 'belum_lunas')->sum('harga_paket');

            // Current month statistics
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $currentMonthPayments = Pembayaran::where('bulan_tagihan', $currentMonth)
                ->where('tahun_tagihan', $currentYear)
                ->count();

            $currentMonthRevenue = Pembayaran::where('bulan_tagihan', $currentMonth)
                ->where('tahun_tagihan', $currentYear)
                ->where('status', 'lunas')
                ->sum('harga_paket');

            // Overdue payments
            $overduePayments = Pembayaran::where('status', 'belum_lunas')
                ->where('created_at', '<', now()->subMonth())
                ->count();

            // Recent payments (last 7 days)
            $recentPayments = Pembayaran::where('status', 'lunas')
                ->where('tanggal_bayar', '>=', now()->subDays(7))
                ->count();

            // Monthly revenue trend (last 6 months)
            $monthlyRevenue = Pembayaran::where('status', 'lunas')
                ->where('created_at', '>=', now()->subMonths(6))
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(harga_paket) as revenue'),
                    DB::raw('COUNT(*) as payment_count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            // Package distribution
            $packageDistribution = Paket::withCount('pelanggans')
                ->orderBy('pelanggans_count', 'desc')
                ->get()
                ->map(function ($package) {
                    return [
                        'nama_paket' => $package->nama_paket,
                        'harga' => $package->harga,
                        'customer_count' => $package->pelanggans_count,
                        'revenue' => $package->pelanggans()->whereHas('pembayarans', function ($q) {
                            $q->where('status', 'lunas');
                        })->with('pembayarans')->get()->sum(function ($customer) {
                            return $customer->pembayarans()->where('status', 'lunas')->sum('harga_paket');
                        })
                    ];
                });

            // Collector performance
            $collectorPerformance = Penagih::withCount(['pelanggans', 'pembayarans'])
                ->get()
                ->map(function ($collector) {
                    $totalRevenue = $collector->pembayarans()->where('status', 'lunas')->sum('harga_paket');
                    $unpaidAmount = $collector->pembayarans()->where('status', 'belum_lunas')->sum('harga_paket');

                    return [
                        'nama' => $collector->nama,
                        'customer_count' => $collector->pelanggans_count,
                        'payment_count' => $collector->pembayarans_count,
                        'total_revenue' => $totalRevenue,
                        'unpaid_amount' => $unpaidAmount,
                        'collection_rate' => $collector->pembayarans_count > 0
                            ? round(($collector->pembayarans()->where('status', 'lunas')->count() / $collector->pembayarans_count) * 100, 2)
                            : 0
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Statistik dashboard berhasil diambil',
                'data' => [
                    'overview' => [
                        'total_customers' => $totalCustomers,
                        'active_customers' => $activeCustomers,
                        'inactive_customers' => $totalCustomers - $activeCustomers,
                        'total_packages' => $totalPackages,
                        'active_packages' => $activePackages,
                        'total_collectors' => $totalCollectors
                    ],
                    'payments' => [
                        'total_payments' => $totalPayments,
                        'paid_payments' => $paidPayments,
                        'unpaid_payments' => $unpaidPayments,
                        'overdue_payments' => $overduePayments,
                        'recent_payments' => $recentPayments,
                        'current_month_payments' => $currentMonthPayments
                    ],
                    'revenue' => [
                        'total_revenue' => $totalRevenue,
                        'unpaid_amount' => $unpaidAmount,
                        'current_month_revenue' => $currentMonthRevenue,
                        'payment_rate' => $totalPayments > 0 ? round(($paidPayments / $totalPayments) * 100, 2) : 0
                    ],
                    'trends' => [
                        'monthly_revenue' => $monthlyRevenue,
                        'package_distribution' => $packageDistribution,
                        'collector_performance' => $collectorPerformance
                    ]
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
     * Get recent activities
     */
    public function recentActivities(): JsonResponse
    {
        try {
            // Recent payments
            $recentPayments = Pembayaran::with(['pelanggan'])
                ->where('status', 'lunas')
                ->where('tanggal_bayar', '>=', now()->subDays(7))
                ->orderBy('tanggal_bayar', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($payment) {
                    return [
                        'type' => 'payment',
                        'message' => "Pembayaran dari {$payment->pelanggan->nama}",
                        'amount' => $payment->harga_paket,
                        'date' => $payment->tanggal_bayar->format('d/m/Y H:i'),
                        'kode_pembayaran' => $payment->kode_pembayaran
                    ];
                });

            // Recent customers
            $recentCustomers = Pelanggan::with(['paket'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($customer) {
                    return [
                        'type' => 'customer',
                        'message' => "Pelanggan baru: {$customer->nama}",
                        'package' => $customer->paket->nama_paket ?? 'Unknown',
                        'date' => $customer->created_at->format('d/m/Y H:i'),
                        'status' => $customer->status
                    ];
                });

            // Overdue payments
            $overduePayments = Pembayaran::with(['pelanggan'])
                ->where('status', 'belum_lunas')
                ->where('created_at', '<', now()->subMonth())
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get()
                ->map(function ($payment) {
                    $daysOverdue = now()->diffInDays($payment->created_at->addMonth());
                    return [
                        'type' => 'overdue',
                        'message' => "Tagihan tertunda: {$payment->pelanggan->nama}",
                        'amount' => $payment->harga_paket,
                        'days_overdue' => $daysOverdue,
                        'kode_pembayaran' => $payment->kode_pembayaran
                    ];
                });

            // Combine and sort activities
            $activities = collect()
                ->merge($recentPayments)
                ->merge($recentCustomers)
                ->merge($overduePayments)
                ->sortByDesc(function ($activity) {
                    return $activity['date'] ?? now()->format('d/m/Y H:i');
                })
                ->take(20)
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas terbaru berhasil diambil',
                'data' => $activities
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
     * Get monthly revenue chart data
     */
    public function monthlyRevenue(): JsonResponse
    {
        try {
            $monthlyData = Pembayaran::where('status', 'lunas')
                ->where('created_at', '>=', now()->subMonths(12))
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(harga_paket) as revenue'),
                    DB::raw('COUNT(*) as payment_count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'year' => $item->year,
                        'month_name' => Carbon::create($item->year, $item->month)->format('M Y'),
                        'revenue' => $item->revenue,
                        'payment_count' => $item->payment_count
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data revenue bulanan berhasil diambil',
                'data' => $monthlyData
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
