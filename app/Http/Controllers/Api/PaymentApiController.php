<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentApiController extends Controller
{
    /**
     * Get all payments with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Pembayaran::with(['pelanggan', 'pelanggan.paket', 'penagih']);

            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode_pembayaran', 'like', "%{$search}%")
                      ->orWhereHas('pelanggan', function ($subQ) use ($search) {
                          $subQ->where('nama', 'like', "%{$search}%")
                               ->orWhere('pppoe', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
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

            // Pagination
            $perPage = $request->get('per_page', 15);
            $payments = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $transformedPayments = $payments->getCollection()->map(function ($payment) {
                $dueDate = $payment->created_at->addMonth();
                $isOverdue = now()->isAfter($dueDate);

                return [
                    'id' => $payment->id,
                    'kode_pembayaran' => $payment->kode_pembayaran,
                    'nama_pelanggan' => $payment->pelanggan->nama,
                    'pppoe' => $payment->pelanggan->pppoe,
                    'no_hp' => $payment->pelanggan->no_hp,
                    'nama_paket' => $payment->pelanggan->paket->nama_paket ?? 'Unknown',
                    'harga_paket' => $payment->harga_paket ?? $payment->pelanggan->paket->harga ?? 0,
                    'periode' => $payment->bulan_tagihan . '/' . $payment->tahun_tagihan,
                    'status' => $payment->status,
                    'tanggal_tagihan' => $payment->created_at->format('d/m/Y'),
                    'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                    'is_overdue' => $isOverdue,
                    'days_overdue' => $isOverdue ? now()->diffInDays($dueDate) : 0,
                    'tanggal_bayar' => $payment->tanggal_bayar ? $payment->tanggal_bayar->format('d/m/Y H:i') : null,
                    'keterangan' => $payment->keterangan,
                    'penagih_nama' => $payment->penagih->nama ?? null
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran berhasil diambil',
                'data' => $transformedPayments,
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem()
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
     * Get payment by ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $payment = Pembayaran::with(['pelanggan', 'pelanggan.paket', 'penagih'])->find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            $dueDate = $payment->created_at->addMonth();
            $isOverdue = now()->isAfter($dueDate);

            $data = [
                'id' => $payment->id,
                'kode_pembayaran' => $payment->kode_pembayaran,
                'nama_pelanggan' => $payment->pelanggan->nama,
                'pppoe' => $payment->pelanggan->pppoe,
                'no_hp' => $payment->pelanggan->no_hp,
                'alamat' => $payment->pelanggan->alamat,
                'nama_paket' => $payment->pelanggan->paket->nama_paket ?? 'Unknown',
                'harga_paket' => $payment->harga_paket ?? $payment->pelanggan->paket->harga ?? 0,
                'periode' => $payment->bulan_tagihan . '/' . $payment->tahun_tagihan,
                'status' => $payment->status,
                'tanggal_tagihan' => $payment->created_at->format('d/m/Y'),
                'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                'is_overdue' => $isOverdue,
                'days_overdue' => $isOverdue ? now()->diffInDays($dueDate) : 0,
                'tanggal_bayar' => $payment->tanggal_bayar ? $payment->tanggal_bayar->format('d/m/Y H:i') : null,
                'keterangan' => $payment->keterangan,
                'penagih_nama' => $payment->penagih->nama ?? null
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran berhasil diambil',
                'data' => $data
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
     * Update payment status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:lunas,belum_lunas',
            'keterangan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $payment = Pembayaran::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            $updateData = [
                'status' => $request->status,
                'keterangan' => $request->keterangan
            ];

            if ($request->status === 'lunas') {
                $updateData['tanggal_bayar'] = now();
            }

            $payment->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui',
                'data' => $payment->load(['pelanggan', 'pelanggan.paket'])
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
     * Mark payment as paid
     */
    public function markPaid(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'keterangan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $payment = Pembayaran::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            $payment->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil ditandai sebagai lunas',
                'data' => $payment->load(['pelanggan', 'pelanggan.paket'])
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
     * Delete payment
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $payment = Pembayaran::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dihapus'
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
     * Generate payments for all customers
     */
    public function generatePayments(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bulan = $request->bulan;
            $tahun = $request->tahun;

            // Check if payments already exist for this period
            $existingPayments = Pembayaran::where('bulan_tagihan', $bulan)
                ->where('tahun_tagihan', $tahun)
                ->count();

            if ($existingPayments > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tagihan untuk bulan {$bulan}/{$tahun} sudah ada"
                ], 400);
            }

            // Get all active customers
            $customers = Pelanggan::with('paket')->where('status', 'aktif')->get();
            $generatedCount = 0;

            foreach ($customers as $customer) {
                // Generate unique payment code
                $kodePembayaran = $this->generateKodePembayaran($customer, $bulan, $tahun);

                Pembayaran::create([
                    'pelanggan_id' => $customer->id,
                    'kode_pembayaran' => $kodePembayaran,
                    'paket_id' => $customer->paket_id,
                    'nama_paket' => $customer->paket->nama_paket,
                    'harga_paket' => $customer->paket->harga,
                    'bulan_tagihan' => $bulan,
                    'tahun_tagihan' => $tahun,
                    'status' => 'belum_lunas',
                    'penagih_id' => $customer->penagih_id,
                    'nama_penagih' => $customer->penagih->nama ?? null
                ]);

                $generatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil generate {$generatedCount} tagihan untuk bulan {$bulan}/{$tahun}",
                'data' => [
                    'generated_count' => $generatedCount,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'generated_at' => now()->format('d/m/Y H:i:s')
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
     * Generate unique payment code
     */
    private function generateKodePembayaran($customer, $bulan, $tahun): string
    {
        $companyProfile = \App\Models\CompanyProfile::first();
        $prefix = $companyProfile->payment_code_prefix ?? 'PAY';

        $customerCode = str_pad($customer->id, 4, '0', STR_PAD_LEFT);
        $monthCode = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $yearCode = substr($tahun, -2);

        return "{$prefix}{$customerCode}{$monthCode}{$yearCode}";
    }
}
