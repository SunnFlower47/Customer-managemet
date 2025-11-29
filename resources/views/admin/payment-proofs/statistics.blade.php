@extends('layouts.app')

@section('title', 'Payment Proof Statistics')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-chart-bar"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Payment Proof Statistics</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Analisis dan statistik bukti pembayaran</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('admin.payment-proofs.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-receipt text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">TOTAL</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">PENDING</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">VERIFIED</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['verified']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">REJECTED</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['rejected']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Period -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="app-card">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Hari Ini</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['today']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Minggu Ini</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Bulan Ini</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- By Submission Method -->
    @if($stats['by_method']->count() > 0)
    <div class="app-card space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">By Submission Method</h2>
            <p class="text-sm text-gray-500">Distribusi berdasarkan metode pengiriman</p>
        </div>
        <div class="space-y-3">
            @foreach($stats['by_method'] as $method => $count)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-paper-plane text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 capitalize">{{ $method ?? 'Unknown' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($count) }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? number_format(($count / $stats['total']) * 100, 1) : 0 }}%</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

