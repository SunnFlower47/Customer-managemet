<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerProfileController extends Controller
{
    /**
     * Get customer profile
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            // Debug logging
            Log::info('Profile show request', [
                'user_id' => $customer ? $customer->id : 'null',
                'user_type' => $customer ? get_class($customer) : 'null',
                'has_customer' => !empty($customer),
                'request_url' => $request->url(),
                'request_method' => $request->method()
            ]);

            if (!$customer) {
                Log::error('No customer found in request');
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ], 401);
            }

            // Eager load relationships to avoid N+1 queries
            $customer->load(['paket', 'penagih']);

            return response()->json([
                'success' => true,
                'message' => 'Data profil berhasil diambil',
                'data' => [
                    'id' => $customer->id,
                    'nama' => $customer->nama,
                    'pppoe' => $customer->pppoe,
                    'alamat' => $customer->alamat,
                    'no_hp' => $customer->no_hp,
                    'status' => $customer->status,
                    'tanggal_mulai' => $customer->tanggal_mulai ? $customer->tanggal_mulai->format('d/m/Y') : null,
                    'tanggal_pembayaran' => $customer->tanggal_pembayaran ? "Tanggal {$customer->tanggal_pembayaran}" : null,
                    'is_default_password' => $customer->is_default_password,
                    'last_login_at' => $customer->last_login_at ? $customer->last_login_at->format('d/m/Y H:i') : null,
                    'paket' => $customer->paket ? [
                        'id' => $customer->paket->id,
                        'nama_paket' => $customer->paket->nama_paket,
                        'harga' => $customer->paket->harga,
                        'deskripsi' => $customer->paket->deskripsi,
                        'aktif' => $customer->paket->aktif,
                    ] : null,
                    'penagih' => $customer->penagih ? [
                        'id' => $customer->penagih->id,
                        'nama' => $customer->penagih->nama,
                        'no_hp' => $customer->penagih->no_hp,
                        'alamat' => $customer->penagih->alamat,
                    ] : null,
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
     * Update customer profile
     */
    public function update(Request $request): JsonResponse
    {
        // Debug logging
        Log::info('Profile update request', [
            'user_id' => $request->user()->id,
            'request_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'no_hp' => 'required|string|max:20|unique:pelanggans,no_hp,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            Log::error('Profile update validation failed', [
                'errors' => $validator->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();

            $updateData = [
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
            ];

            Log::info('Updating customer profile', [
                'customer_id' => $customer->id,
                'update_data' => $updateData
            ]);

            $result = $customer->update($updateData);

            Log::info('Profile update result', [
                'success' => $result,
                'updated_customer' => $customer->fresh()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'id' => $customer->id,
                    'nama' => $customer->nama,
                    'pppoe' => $customer->pppoe,
                    'alamat' => $customer->alamat,
                    'no_hp' => $customer->no_hp,
                    'status' => $customer->status,
                    'updated_at' => $customer->updated_at->format('d/m/Y H:i'),
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
     * Change customer password
     */
    public function changePassword(Request $request): JsonResponse
    {
        // Debug logging
        Log::info('Change password request', [
            'user_id' => $request->user()->id,
            'has_current_password' => $request->has('current_password'),
            'has_new_password' => $request->has('new_password'),
            'has_confirmation' => $request->has('new_password_confirmation')
        ]);

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Log::error('Change password validation failed', [
                'errors' => $validator->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();

            // Verify current password
            $currentPasswordValid = Hash::check($request->current_password, $customer->password);
            Log::info('Password verification', [
                'current_password_valid' => $currentPasswordValid,
                'customer_has_password' => !empty($customer->password)
            ]);

            if (!$currentPasswordValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama salah'
                ], 401);
            }

            // Update password
            $newPasswordHash = Hash::make($request->new_password);
            Log::info('Updating password', [
                'customer_id' => $customer->id,
                'new_password_length' => strlen($request->new_password)
            ]);

            $result = $customer->update([
                'password' => $newPasswordHash,
                'is_default_password' => false
            ]);

            Log::info('Password update result', [
                'success' => $result,
                'customer_updated' => $customer->fresh()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
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
     * Test endpoint for debugging
     */
    public function test(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            Log::info('Test endpoint called', [
                'user_id' => $customer ? $customer->id : 'null',
                'user_type' => $customer ? get_class($customer) : 'null',
                'has_customer' => !empty($customer),
                'request_url' => $request->url(),
                'request_method' => $request->method(),
                'headers' => $request->headers->all(),
                'bearer_token' => $request->bearerToken()
            ]);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan',
                    'debug' => [
                        'has_user' => false,
                        'request_url' => $request->url(),
                        'method' => $request->method(),
                        'bearer_token' => $request->bearerToken() ? 'present' : 'missing'
                    ]
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Test endpoint berhasil',
                'data' => [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->nama,
                    'customer_pppoe' => $customer->pppoe,
                    'customer_status' => $customer->status,
                    'has_password' => !empty($customer->password),
                    'is_default_password' => $customer->is_default_password,
                    'last_login' => $customer->last_login_at,
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Test endpoint error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get customer statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            // Payment statistics
            $totalPayments = $customer->pembayarans()->count();
            $paidPayments = $customer->pembayarans()->where('status', 'lunas')->count();
            $unpaidPayments = $customer->pembayarans()->where('status', 'belum_bayar')->count();
            $totalPaid = $customer->pembayarans()->where('status', 'lunas')->sum('jumlah');

            // Ticket statistics
            $totalTickets = \App\Models\Ticket::where('pelanggan_id', $customer->id)->count();
            $openTickets = \App\Models\Ticket::where('pelanggan_id', $customer->id)->where('status', 'open')->count();
            $resolvedTickets = \App\Models\Ticket::where('pelanggan_id', $customer->id)->where('status', 'resolved')->count();

            // Recent activity
            $recentPayments = $customer->pembayarans()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'kode_pembayaran' => $payment->kode_pembayaran,
                        'jumlah' => $payment->jumlah,
                        'status' => $payment->status,
                        'bulan_tagihan' => $payment->bulan_tagihan,
                        'tahun_tagihan' => $payment->tahun_tagihan,
                        'created_at' => $payment->created_at->format('d/m/Y'),
                    ];
                });

            $recentTickets = \App\Models\Ticket::where('pelanggan_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'kode_ticket' => $ticket->kode_ticket,
                        'judul' => $ticket->judul,
                        'status' => $ticket->status,
                        'kategori' => $ticket->kategori,
                        'created_at' => $ticket->created_at->format('d/m/Y'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Statistik berhasil diambil',
                'data' => [
                    // Flat structure for frontend compatibility
                    'total_payments' => $totalPayments,
                    'paid_payments' => $paidPayments,
                    'unpaid_payments' => $unpaidPayments,
                    'total_paid' => $totalPaid,
                    'total_tickets' => $totalTickets,
                    'open_tickets' => $openTickets,
                    'resolved_tickets' => $resolvedTickets,
                    // Nested structure for detailed access
                    'payment_stats' => [
                        'total_payments' => $totalPayments,
                        'paid_payments' => $paidPayments,
                        'unpaid_payments' => $unpaidPayments,
                        'total_paid' => $totalPaid,
                    ],
                    'ticket_stats' => [
                        'total_tickets' => $totalTickets,
                        'open_tickets' => $openTickets,
                        'resolved_tickets' => $resolvedTickets,
                    ],
                    'recent_activity' => [
                        'recent_payments' => $recentPayments,
                        'recent_tickets' => $recentTickets,
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
}
