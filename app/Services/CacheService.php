<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Paket;
use App\Models\Penagih;
use App\Models\CompanyProfile;

class CacheService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 5; // 5 minutes

    /**
     * Get dashboard statistics with caching
     */
    public static function getDashboardStats()
    {
        return Cache::remember('dashboard_stats', self::CACHE_DURATION, function () {
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $lastMonth = now()->subMonth()->month;
            $lastYear = now()->subMonth()->year;

            // Current month data
            $currentCustomers = Pelanggan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)->count();
            $lastMonthCustomers = Pelanggan::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $lastMonth)->count();

            $currentRevenue = Pembayaran::where('status', 'lunas')
                ->where('bulan_tagihan', $currentMonth)
                ->where('tahun_tagihan', $currentYear)
                ->sum('jumlah');
            $lastMonthRevenue = Pembayaran::where('status', 'lunas')
                ->where('bulan_tagihan', $lastMonth)
                ->where('tahun_tagihan', $lastYear)
                ->sum('jumlah');

            // Calculate percentage changes
            $customerGrowth = $lastMonthCustomers > 0
                ? round((($currentCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
                : ($currentCustomers > 0 ? 'new' : 0);

            $revenueGrowth = $lastMonthRevenue > 0
                ? round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : ($currentRevenue > 0 ? 'new' : 0);

            return [
                'total_pelanggan' => Pelanggan::count(),
                'total_paket' => Paket::where('aktif', true)->count(),
                'total_penagih' => Penagih::where('aktif', true)->count(),
                'total_customers' => Pelanggan::count(),
                'active_customers' => Pelanggan::where('status', 'aktif')->count(),
                'total_payments' => Pembayaran::count(),
                'paid_payments' => Pembayaran::where('status', 'lunas')->count(),
                'unpaid_payments' => Pembayaran::where('status', 'belum_bayar')->count(),
                'total_packages' => Paket::count(),
                'active_packages' => Paket::where('aktif', true)->count(),
                'total_collectors' => Penagih::count(),
                'active_collectors' => Penagih::where('aktif', true)->count(),
                // Growth data
                'customer_growth' => $customerGrowth,
                'revenue_growth' => $revenueGrowth,
                'current_month_customers' => $currentCustomers,
                'last_month_customers' => $lastMonthCustomers,
                'current_month_revenue' => $currentRevenue,
                'last_month_revenue' => $lastMonthRevenue,
            ];
        });
    }

    /**
     * Get monthly revenue with caching
     */
    public static function getMonthlyRevenue($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $cacheKey = "monthly_revenue_{$year}_{$month}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($month, $year) {
            return Pembayaran::where('status', 'lunas')
                ->where('bulan_tagihan', $month)
                ->where('tahun_tagihan', $year)
                ->sum('jumlah');
        });
    }

    /**
     * Get recent activities with caching
     */
    public static function getRecentActivities($limit = 10)
    {
        return Cache::remember("recent_activities_{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return [
                'recent_payments' => Pembayaran::with(['pelanggan:id,nama'])
                    ->latest()
                    ->take($limit)
                    ->get(),
                'recent_customers' => Pelanggan::with(['paket:id,nama_paket'])
                    ->latest()
                    ->take($limit)
                    ->get(),
            ];
        });
    }

    /**
     * Get growth statistics for specific metrics
     */
    public static function getGrowthStats()
    {
        return Cache::remember('growth_stats', self::CACHE_DURATION, function () {
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $lastMonth = now()->subMonth()->month;
            $lastYear = now()->subMonth()->year;

            // Customer growth
            $currentCustomers = Pelanggan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)->count();
            $lastMonthCustomers = Pelanggan::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $lastMonth)->count();

            // Revenue growth
            $currentRevenue = Pembayaran::where('status', 'lunas')
                ->where('bulan_tagihan', $currentMonth)
                ->where('tahun_tagihan', $currentYear)
                ->sum('jumlah');
            $lastMonthRevenue = Pembayaran::where('status', 'lunas')
                ->where('bulan_tagihan', $lastMonth)
                ->where('tahun_tagihan', $lastYear)
                ->sum('jumlah');

            // Payment growth
            $currentPayments = Pembayaran::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)->count();
            $lastMonthPayments = Pembayaran::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $lastMonth)->count();

            return [
                'customers' => [
                    'current' => $currentCustomers,
                    'last_month' => $lastMonthCustomers,
                    'growth' => $lastMonthCustomers > 0
                        ? round((($currentCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
                        : ($currentCustomers > 0 ? 'new' : 0)
                ],
                'revenue' => [
                    'current' => $currentRevenue,
                    'last_month' => $lastMonthRevenue,
                    'growth' => $lastMonthRevenue > 0
                        ? round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                        : ($currentRevenue > 0 ? 'new' : 0)
                ],
                'payments' => [
                    'current' => $currentPayments,
                    'last_month' => $lastMonthPayments,
                    'growth' => $lastMonthPayments > 0
                        ? round((($currentPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 1)
                        : ($currentPayments > 0 ? 'new' : 0)
                ]
            ];
        });
    }

    /**
     * Clear all dashboard caches
     */
    public static function clearDashboardCache()
    {
        Cache::forget('dashboard_stats');
        Cache::forget('growth_stats');
        Cache::forget('recent_activities_10');

        // Clear monthly revenue caches for current year
        for ($month = 1; $month <= 12; $month++) {
            Cache::forget("monthly_revenue_" . now()->year . "_{$month}");
        }
    }

    /**
     * Clear specific cache by key
     */
    public static function clearCache($key)
    {
        Cache::forget($key);
    }

    /**
     * Get company profile with caching
     */
    public static function getCompanyProfile()
    {
        return Cache::remember('company_profile', 60, function () {
            return CompanyProfile::first();
        });
    }

    /**
     * Get payment methods from company profile with caching
     */
    public static function getPaymentMethods()
    {
        return Cache::remember('payment_methods', 60, function () {
            $profile = CompanyProfile::first();
            if (!$profile) {
                return null;
            }

            return [
                'dana_phone' => $profile->dana_phone,
                'mandiri_account' => $profile->mandiri_account,
                'mandiri_account_name' => $profile->mandiri_account_name,
                'payment_whatsapp' => $profile->payment_whatsapp,
                'company_name' => $profile->nama_perusahaan,
            ];
        });
    }

    /**
     * Clear company profile cache
     */
    public static function clearCompanyProfileCache()
    {
        Cache::forget('company_profile');
        Cache::forget('payment_methods');
    }
}
