<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Paket;
use App\Models\Penagih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptimizedController extends Controller
{
    /**
     * Optimized method to get customers with minimal queries
     */
    public function getCustomersOptimized(Request $request)
    {
        $query = Pelanggan::with(['paket:id,nama_paket,harga', 'penagih:id,nama'])
            ->select(['id', 'nama', 'pppoe', 'alamat', 'no_hp', 'status', 'paket_id', 'penagih_id', 'created_at']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('pppoe', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

        if ($request->filled('penagih_id')) {
            $query->where('penagih_id', $request->penagih_id);
        }

        return $query->paginate(15);
    }

    /**
     * Optimized method to get payments with minimal queries
     */
    public function getPaymentsOptimized(Request $request)
    {
        $query = Pembayaran::with([
                'pelanggan:id,nama,pppoe,no_hp',
                'penagih:id,nama',
                'paket:id,nama_paket'
            ])
            ->select([
                'id', 'kode_pembayaran', 'pelanggan_id', 'penagih_id', 'paket_id',
                'nama_paket', 'harga_paket', 'bulan_tagihan', 'tahun_tagihan',
                'jumlah', 'status', 'tanggal_bayar', 'created_at'
            ]);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('pppoe', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan_tagihan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_tagihan', $request->tahun);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Optimized method to get dashboard statistics
     */
    public function getDashboardStatsOptimized()
    {
        // Use single query with conditional aggregation
        $stats = DB::table('pelanggans')
            ->selectRaw('
                COUNT(*) as total_customers,
                SUM(CASE WHEN status IN ("aktif", "bayar double") THEN 1 ELSE 0 END) as active_customers,
                SUM(CASE WHEN status = "isolir" THEN 1 ELSE 0 END) as inactive_customers,
                SUM(CASE WHEN status = "bayar double" THEN 1 ELSE 0 END) as suspended_customers,
                SUM(CASE WHEN status = "nonaktif" THEN 1 ELSE 0 END) as nonaktif_customers
            ')
            ->first();

        $paymentStats = DB::table('pembayarans')
            ->selectRaw('
                COUNT(*) as total_payments,
                SUM(CASE WHEN status = "lunas" THEN 1 ELSE 0 END) as paid_payments,
                SUM(CASE WHEN status = "belum_bayar" THEN 1 ELSE 0 END) as unpaid_payments,
                SUM(CASE WHEN status = "lunas" THEN jumlah ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status = "belum_bayar" THEN jumlah ELSE 0 END) as unpaid_amount
            ')
            ->first();

        return [
            'customers' => $stats,
            'payments' => $paymentStats,
            'packages' => Paket::where('aktif', true)->count(),
            'collectors' => Penagih::where('aktif', true)->count(),
        ];
    }

    /**
     * Optimized method to get monthly revenue data
     */
    public function getMonthlyRevenueOptimized($year = null)
    {
        $year = $year ?? now()->year;

        return DB::table('pembayarans')
            ->selectRaw('
                bulan_tagihan as month,
                SUM(CASE WHEN status = "lunas" THEN jumlah ELSE 0 END) as revenue,
                COUNT(CASE WHEN status = "lunas" THEN 1 END) as paid_count,
                COUNT(CASE WHEN status = "belum_bayar" THEN 1 END) as unpaid_count
            ')
            ->where('tahun_tagihan', $year)
            ->groupBy('bulan_tagihan')
            ->orderBy('bulan_tagihan')
            ->get();
    }

    /**
     * Optimized method to get recent activities
     */
    public function getRecentActivitiesOptimized($limit = 10)
    {
        // Get recent payments
        $recentPayments = Pembayaran::with(['pelanggan:id,nama'])
            ->select(['id', 'kode_pembayaran', 'pelanggan_id', 'jumlah', 'status', 'created_at'])
            ->latest()
            ->limit($limit)
            ->get();

        // Get recent customers
        $recentCustomers = Pelanggan::with(['paket:id,nama_paket'])
            ->select(['id', 'nama', 'pppoe', 'status', 'paket_id', 'created_at'])
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'payments' => $recentPayments,
            'customers' => $recentCustomers,
        ];
    }
}
