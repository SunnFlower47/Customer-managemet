<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Penagih;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Get all customers with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Pelanggan::with(['paket', 'penagih']);

            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('pppoe', 'like', "%{$search}%")
                      ->orWhere('no_hp', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            if ($request->has('paket_id') && $request->paket_id) {
                $query->where('paket_id', $request->paket_id);
            }

            if ($request->has('penagih_id') && $request->penagih_id) {
                $query->where('penagih_id', $request->penagih_id);
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $customers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil',
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'last_page' => $customers->lastPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'from' => $customers->firstItem(),
                    'to' => $customers->lastItem()
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
     * Get customer by ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $customer = Pelanggan::with(['paket', 'penagih'])->find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil',
                'data' => $customer
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
     * Create new customer
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'paket_id' => 'required|exists:pakets,id',
            'penagih_id' => 'nullable|exists:penagihs,id',
            'tanggal_pembayaran' => 'nullable|integer|min:1|max:31',
            'status' => 'required|in:aktif,isolir,bayar double,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Pelanggan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan',
                'data' => $customer->load(['paket', 'penagih'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update customer
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $customer = Pelanggan::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe,' . $id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'paket_id' => 'required|exists:pakets,id',
            'penagih_id' => 'nullable|exists:penagihs,id',
            'tanggal_pembayaran' => 'nullable|integer|min:1|max:31',
            'status' => 'required|in:aktif,isolir,bayar double,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diperbarui',
                'data' => $customer->load(['paket', 'penagih'])
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
     * Delete customer
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $customer = Pelanggan::find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus'
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
     */
    public function paymentHistory(string $id): JsonResponse
    {
        try {
            $customer = Pelanggan::find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            $payments = $customer->pembayarans()
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat pembayaran berhasil diambil',
                'data' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total()
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
