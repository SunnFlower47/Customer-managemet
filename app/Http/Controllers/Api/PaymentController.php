<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Check customer bills by PPPoE or Phone Number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkCustomerBills(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pppoe' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'pelanggan_id' => 'nullable|integer|exists:pelanggans,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        // Check if at least one identifier is provided
        if (!$request->pppoe && !$request->no_hp && !$request->pelanggan_id) {
            return response()->json([
                'success' => false,
                'message' => 'Harus menyediakan salah satu: pppoe, no_hp, atau pelanggan_id',
                'errors' => ['identifier' => 'At least one identifier is required']
            ], 400);
        }

        try {
            $pelanggan = null;

            // Find customer by different identifiers
            if ($request->pelanggan_id) {
                $pelanggan = Pelanggan::with(['paket'])->find($request->pelanggan_id);
            } elseif ($request->pppoe) {
                $pelanggan = Pelanggan::with(['paket'])->where('pppoe', $request->pppoe)->first();
            } elseif ($request->no_hp) {
                $pelanggan = Pelanggan::with(['paket'])->where('no_hp', $request->no_hp)->first();
            }

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Get all unpaid bills
            $unpaidBills = Pembayaran::where('pelanggan_id', $pelanggan->id)
                ->where('status', '!=', 'lunas')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get latest bill
            $latestBill = Pembayaran::where('pelanggan_id', $pelanggan->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $bills = $unpaidBills->map(function ($pembayaran) {
                $dueDate = $pembayaran->created_at->addMonth();
                $isOverdue = now()->isAfter($dueDate);

                return [
                    'kode_pembayaran' => $pembayaran->kode_pembayaran,
                    'periode' => $pembayaran->bulan_tagihan . '/' . $pembayaran->tahun_tagihan,
                    'status' => $pembayaran->status,
                    'harga' => $pembayaran->harga_paket ?? $pembayaran->pelanggan->paket->harga ?? 0,
                    'tanggal_tagihan' => $pembayaran->created_at->format('d/m/Y'),
                    'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                    'is_overdue' => $isOverdue,
                    'days_overdue' => $isOverdue ? now()->diffInDays($dueDate) : 0
                ];
            });

            $response = [
                'success' => true,
                'message' => 'Data tagihan ditemukan',
                'data' => [
                    'pelanggan_id' => $pelanggan->id,
                    'nama_pelanggan' => $pelanggan->nama,
                    'pppoe' => $pelanggan->pppoe,
                    'no_hp' => $pelanggan->no_hp,
                    'alamat' => $pelanggan->alamat,
                    'nama_paket' => $pelanggan->paket->nama_paket ?? 'Paket tidak ditemukan',
                    'harga_paket' => $pelanggan->paket->harga ?? 0,
                    'total_unpaid_bills' => $unpaidBills->count(),
                    'total_amount_due' => $unpaidBills->sum('harga_paket'),
                    'latest_bill_code' => $latestBill ? $latestBill->kode_pembayaran : null,
                    'unpaid_bills' => $bills
                ]
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check payment status by payment code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_pembayaran' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $kodePembayaran = $request->kode_pembayaran;

            // Find payment by code
            $pembayaran = Pembayaran::with(['pelanggan', 'pelanggan.paket'])
                ->where('kode_pembayaran', $kodePembayaran)
                ->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pembayaran tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Calculate due date
            $dueDate = $pembayaran->created_at->addMonth();
            $isOverdue = now()->isAfter($dueDate);

            $response = [
                'success' => true,
                'message' => 'Data tagihan ditemukan',
                'data' => [
                    'kode_pembayaran' => $pembayaran->kode_pembayaran,
                    'nama_pelanggan' => $pembayaran->pelanggan->nama,
                    'no_hp' => $pembayaran->pelanggan->no_hp,
                    'alamat' => $pembayaran->pelanggan->alamat,
                    'nama_paket' => $pembayaran->pelanggan->paket->nama_paket ?? 'Paket tidak ditemukan',
                    'harga_paket' => $pembayaran->harga_paket ?? $pembayaran->pelanggan->paket->harga ?? 0,
                    'periode' => $pembayaran->bulan_tagihan . '/' . $pembayaran->tahun_tagihan,
                    'status' => $pembayaran->status,
                    'tanggal_tagihan' => $pembayaran->created_at->format('d/m/Y'),
                    'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                    'is_overdue' => $isOverdue,
                    'days_overdue' => $isOverdue ? now()->diffInDays($dueDate) : 0,
                    'tanggal_bayar' => $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y H:i') : null,
                    'keterangan' => $pembayaran->keterangan
                ]
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Verify payment (for payment gateway integration)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_pembayaran' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $kodePembayaran = $request->kode_pembayaran;

            // Find payment by code
            $pembayaran = Pembayaran::where('kode_pembayaran', $kodePembayaran)->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pembayaran tidak ditemukan',
                    'error_code' => 'PAYMENT_NOT_FOUND'
                ], 404);
            }

            if ($pembayaran->status === 'lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran sudah lunas',
                    'error_code' => 'PAYMENT_ALREADY_PAID'
                ], 400);
            }

            // Check if amount matches (allow small tolerance for rounding)
            if (abs($request->amount - $pembayaran->harga_paket) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran tidak sesuai',
                    'error_code' => 'AMOUNT_MISMATCH',
                    'expected_amount' => $pembayaran->harga_paket,
                    'received_amount' => $request->amount
                ], 400);
            }

            // Mark as paid
            $pembayaran->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
                'keterangan' => $request->notes ?? 'Pembayaran via Payment Gateway'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diverifikasi',
                'data' => [
                    'kode_pembayaran' => $pembayaran->kode_pembayaran,
                    'status' => $pembayaran->status,
                    'tanggal_bayar' => $pembayaran->tanggal_bayar->format('d/m/Y H:i'),
                    'amount' => $pembayaran->harga_paket,
                    'transaction_id' => $request->transaction_id,
                    'payment_method' => $request->payment_method
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
     * Get customer payment history
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'no_hp' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $noHp = $request->no_hp;

            // Find customer by phone number
            $pelanggan = Pelanggan::with(['paket'])->where('no_hp', $noHp)->first();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor HP tidak terdaftar',
                    'data' => null
                ], 404);
            }

            // Get payment history
            $pembayarans = Pembayaran::where('pelanggan_id', $pelanggan->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $paymentHistory = $pembayarans->map(function ($pembayaran) {
                $dueDate = $pembayaran->created_at->addMonth();
                $isOverdue = now()->isAfter($dueDate);

                return [
                    'kode_pembayaran' => $pembayaran->kode_pembayaran,
                    'periode' => $pembayaran->bulan_tagihan . '/' . $pembayaran->tahun_tagihan,
                    'status' => $pembayaran->status,
                    'harga' => $pembayaran->harga_paket ?? $pembayaran->pelanggan->paket->harga ?? 0,
                    'tanggal_tagihan' => $pembayaran->created_at->format('d/m/Y'),
                    'tanggal_jatuh_tempo' => $dueDate->format('d/m/Y'),
                    'is_overdue' => $isOverdue,
                    'tanggal_bayar' => $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y H:i') : null
                ];
            });

            $response = [
                'success' => true,
                'message' => 'Riwayat pembayaran ditemukan',
                'data' => [
                    'nama_pelanggan' => $pelanggan->nama,
                    'no_hp' => $pelanggan->no_hp,
                    'alamat' => $pelanggan->alamat,
                    'nama_paket' => $pelanggan->paket->nama_paket ?? 'Paket tidak ditemukan',
                    'harga_paket' => $pelanggan->paket->harga ?? 0,
                    'payment_history' => $paymentHistory
                ]
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
