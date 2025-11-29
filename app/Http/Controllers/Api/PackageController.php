<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    /**
     * Get all packages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Paket::query();

            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_paket', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            if ($request->has('aktif') && $request->aktif !== '') {
                $query->where('aktif', $request->boolean('aktif'));
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $packages = $query->orderBy('nama_paket')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data paket berhasil diambil',
                'data' => $packages->items(),
                'pagination' => [
                    'current_page' => $packages->currentPage(),
                    'last_page' => $packages->lastPage(),
                    'per_page' => $packages->perPage(),
                    'total' => $packages->total(),
                    'from' => $packages->firstItem(),
                    'to' => $packages->lastItem()
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
     * Get package by ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $package = Paket::find($id);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data paket berhasil diambil',
                'data' => $package
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
     * Create new package
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $package = Paket::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil ditambahkan',
                'data' => $package
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
     * Update package
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $package = Paket::find($id);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'aktif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $package->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil diperbarui',
                'data' => $package
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
     * Delete package
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $package = Paket::find($id);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan'
                ], 404);
            }

            // Check if package is being used by customers
            $customerCount = $package->pelanggans()->count();
            if ($customerCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Paket tidak dapat dihapus karena masih digunakan oleh {$customerCount} pelanggan"
                ], 400);
            }

            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil dihapus'
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
     * Get package statistics
     */
    public function statistics(string $id): JsonResponse
    {
        try {
            $package = Paket::find($id);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan'
                ], 404);
            }

            $totalCustomers = $package->pelanggans()->count();
            $activeCustomers = $package->pelanggans()->where('status', 'aktif')->count();
            $inactiveCustomers = $package->pelanggans()->where('status', 'isolir')->count();

            $totalRevenue = $package->pelanggans()
                ->whereHas('pembayarans', function ($q) {
                    $q->where('status', 'lunas');
                })
                ->with('pembayarans')
                ->get()
                ->sum(function ($customer) {
                    return $customer->pembayarans()->where('status', 'lunas')->sum('harga_paket');
                });

            $unpaidAmount = $package->pelanggans()
                ->whereHas('pembayarans', function ($q) {
                    $q->where('status', 'belum_lunas');
                })
                ->with('pembayarans')
                ->get()
                ->sum(function ($customer) {
                    return $customer->pembayarans()->where('status', 'belum_lunas')->sum('harga_paket');
                });

            return response()->json([
                'success' => true,
                'message' => 'Statistik paket berhasil diambil',
                'data' => [
                    'package_info' => $package,
                    'total_customers' => $totalCustomers,
                    'active_customers' => $activeCustomers,
                    'inactive_customers' => $inactiveCustomers,
                    'total_revenue' => $totalRevenue,
                    'unpaid_amount' => $unpaidAmount,
                    'revenue_percentage' => $totalCustomers > 0 ? round(($activeCustomers / $totalCustomers) * 100, 2) : 0
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
