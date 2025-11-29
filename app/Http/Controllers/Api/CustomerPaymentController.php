<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\CompanyProfile;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerPaymentController extends Controller
{
    /**
     * Get customer's unpaid bills
     */
    public function getUnpaidBills(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            // Batas waktu: ambil semua tagihan hingga periode berjalan
            $currentYear = (int) now()->year;
            $currentMonth = (int) now()->month;

            $unpaidBills = Pembayaran::with(['paket', 'penagih', 'pelanggan'])
                ->where('pelanggan_id', $customer->id)
                ->where('status', 'belum_bayar')
                ->where(function ($q) use ($currentYear, $currentMonth) {
                    $q->where('tahun_tagihan', '<', $currentYear)
                      ->orWhere(function ($q) use ($currentYear, $currentMonth) {
                          $q->where('tahun_tagihan', $currentYear)
                            ->where('bulan_tagihan', '<=', $currentMonth);
                      });
                })
                ->orderByRaw('CAST(tahun_tagihan AS UNSIGNED) ASC')
                ->orderByRaw("LPAD(CAST(bulan_tagihan AS CHAR), 2, '0') ASC")
                ->get()
                ->map(function ($pembayaran) {
                    $customer = $pembayaran->pelanggan;
                    $day = $customer && $customer->tanggal_pembayaran ? (int) $customer->tanggal_pembayaran : 15;
                    $dueDate = \Carbon\Carbon::create(
                        (int) $pembayaran->tahun_tagihan,
                        (int) $pembayaran->bulan_tagihan,
                        min(max($day, 1), 28)
                    )->format('d/m/Y');
                    return [
                        'id' => $pembayaran->id,
                        'kode_pembayaran' => $pembayaran->kode_pembayaran,
                        'bulan_tagihan' => $pembayaran->bulan_tagihan,
                        'tahun_tagihan' => $pembayaran->tahun_tagihan,
                        'jumlah' => $pembayaran->jumlah,
                        'status' => $pembayaran->status,
                        'created_at' => $pembayaran->created_at->format('d/m/Y'),
                        'due_date' => $dueDate,
                        'package_info' => [
                            'nama_paket' => $pembayaran->historicalPackageName,
                            'harga_paket' => $pembayaran->historicalPackagePrice,
                        ],
                        'collector_info' => [
                            'nama_penagih' => $pembayaran->nama_penagih ?: ($pembayaran->penagih ? $pembayaran->penagih->nama : 'Belum ada penagih'),
                            'no_hp_penagih' => $pembayaran->penagih ? $pembayaran->penagih->no_hp : null,
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data tagihan berhasil diambil',
                'data' => $unpaidBills
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
     * Get customer's payment history
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            $paymentHistory = Pembayaran::with(['paket', 'penagih', 'pelanggan'])
                ->where('pelanggan_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->through(function ($pembayaran) {
                    return [
                        'id' => $pembayaran->id,
                        'kode_pembayaran' => $pembayaran->kode_pembayaran,
                        'bulan_tagihan' => $pembayaran->bulan_tagihan,
                        'tahun_tagihan' => $pembayaran->tahun_tagihan,
                        'jumlah' => $pembayaran->jumlah,
                        'status' => $pembayaran->status,
                        'tanggal_bayar' => $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y H:i') : null,
                        'created_at' => $pembayaran->created_at->format('d/m/Y'),
                        'package_info' => [
                            'nama_paket' => $pembayaran->historicalPackageName,
                            'harga_paket' => $pembayaran->historicalPackagePrice,
                        ],
                        'collector_info' => [
                            'nama_penagih' => $pembayaran->nama_penagih ?: ($pembayaran->penagih ? $pembayaran->penagih->nama : 'Belum ada penagih'),
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Riwayat pembayaran berhasil diambil',
                'data' => $paymentHistory->items(),
                'pagination' => [
                    'current_page' => $paymentHistory->currentPage(),
                    'last_page' => $paymentHistory->lastPage(),
                    'per_page' => $paymentHistory->perPage(),
                    'total' => $paymentHistory->total(),
                    'from' => $paymentHistory->firstItem(),
                    'to' => $paymentHistory->lastItem()
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
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pembayaran_id' => 'required|integer|exists:pembayarans,id',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();
            $pembayaran = Pembayaran::findOrFail($request->pembayaran_id);

            // Verify that the payment belongs to the customer
            if ($pembayaran->pelanggan_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            // Check if payment is already paid
            if ($pembayaran->status === 'lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran sudah lunas'
                ], 400);
            }

            // Handle file upload
            $file = $request->file('proof_file');
            $filename = 'payment_proof_' . $pembayaran->kode_pembayaran . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('payment_proofs', $filename, 'public');

            // Create payment proof record
            $paymentProof = PaymentProof::create([
                'pembayaran_id' => $pembayaran->id,
                'pelanggan_id' => $customer->id,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'status' => 'pending',
                'submission_method' => 'website_upload',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload',
                'data' => [
                    'payment_proof_id' => $paymentProof->id,
                    'pembayaran_id' => $pembayaran->id,
                    'kode_pembayaran' => $pembayaran->kode_pembayaran,
                    'status' => 'pending',
                    'message' => 'Bukti pembayaran sedang diproses oleh admin'
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
     * Send payment proof to WhatsApp
     */
    public function sendToWhatsApp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pembayaran_id' => 'required|integer|exists:pembayarans,id',
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();
            $pembayaran = Pembayaran::findOrFail($request->pembayaran_id);

            // Verify that the payment belongs to the customer
            if ($pembayaran->pelanggan_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            // Get company WhatsApp number
            $companyProfile = CompanyProfile::first();
            if (!$companyProfile || !$companyProfile->whatsapp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp perusahaan tidak tersedia'
                ], 400);
            }

            // Prepare message
            $defaultMessage = "Halo, saya ingin mengirim bukti pembayaran untuk:\n";
            $defaultMessage .= "Kode Pembayaran: {$pembayaran->kode_pembayaran}\n";
            $defaultMessage .= "Nama: {$customer->nama}\n";
            $defaultMessage .= "Nomor HP: {$customer->no_hp}\n";
            $defaultMessage .= "Jumlah: Rp " . number_format((float)$pembayaran->jumlah, 0, ',', '.') . "\n";
            $defaultMessage .= "Bulan: {$pembayaran->bulan_tagihan}/{$pembayaran->tahun_tagihan}";

            $message = $request->message ?: $defaultMessage;
            $whatsappNumber = $companyProfile->formatted_whatsapp;
            $whatsappLink = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

            return response()->json([
                'success' => true,
                'message' => 'Link WhatsApp berhasil dibuat',
                'data' => [
                    'whatsapp_link' => $whatsappLink,
                    'whatsapp_number' => $whatsappNumber,
                    'message' => $message,
                    'pembayaran_id' => $pembayaran->id,
                    'kode_pembayaran' => $pembayaran->kode_pembayaran
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
     * Get payment status
     */
    public function getPaymentStatus(Request $request, $id): JsonResponse
    {
        try {
            $customer = $request->user();
            $pembayaran = Pembayaran::findOrFail($id);

            // Verify that the payment belongs to the customer
            if ($pembayaran->pelanggan_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            // Get payment proofs
            $paymentProofs = PaymentProof::where('pembayaran_id', $pembayaran->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($proof) {
                    return [
                        'id' => $proof->id,
                        'file_name' => $proof->file_name,
                        'file_type' => $proof->file_type,
                        'file_size' => $proof->formatted_file_size,
                        'file_url' => $proof->file_url,
                        'status' => $proof->status,
                        'submission_method' => $proof->submission_method,
                        'created_at' => $proof->created_at->format('d/m/Y H:i'),
                        'admin_notes' => $proof->admin_notes,
                        'verified_at' => $proof->verified_at ? $proof->verified_at->format('d/m/Y H:i') : null,
                        'verified_by' => $proof->verifiedBy ? $proof->verifiedBy->name : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diambil',
                'data' => [
                    'pembayaran' => [
                        'id' => $pembayaran->id,
                        'kode_pembayaran' => $pembayaran->kode_pembayaran,
                        'bulan_tagihan' => $pembayaran->bulan_tagihan,
                        'tahun_tagihan' => $pembayaran->tahun_tagihan,
                        'jumlah' => $pembayaran->jumlah,
                        'status' => $pembayaran->status,
                        'tanggal_bayar' => $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y H:i') : null,
                        'created_at' => $pembayaran->created_at->format('d/m/Y'),
                    ],
                    'payment_proofs' => $paymentProofs
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
     * Calculate due date for payment
     */
    private function calculateDueDate($pembayaran)
    {
        // Due date is 30 days after payment creation
        return $pembayaran->created_at->addDays(30)->format('d/m/Y');
    }
}
