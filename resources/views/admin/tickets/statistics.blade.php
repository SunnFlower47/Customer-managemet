@extends('layouts.app')

@section('title', 'Ticket Statistics')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-chart-bar"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-amber-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Ticket Statistics</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Analisis dan statistik tiket pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('admin.tickets.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-ticket-alt text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">TOTAL TIKET</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">OPEN</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['open']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">IN PROGRESS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['in_progress']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">RESOLVED</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['resolved']) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-gray-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">CLOSED</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['closed']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- By Category -->
        <div class="app-card space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">By Category</h2>
                <p class="text-sm text-gray-500">Distribusi tiket berdasarkan kategori</p>
            </div>
            <div class="space-y-3">
                @forelse($stats['by_category'] as $category => $count)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-folder text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ $category }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">{{ number_format($count) }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? number_format(($count / $stats['total']) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <i class="fas fa-chart-pie text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada data kategori</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- By Priority -->
        <div class="app-card space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">By Priority</h2>
                <p class="text-sm text-gray-500">Distribusi tiket berdasarkan prioritas</p>
            </div>
            <div class="space-y-3">
                @forelse($stats['by_priority'] as $priority => $count)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center
                            @if($priority === 'urgent') bg-red-50 text-red-600
                            @elseif($priority === 'high') bg-orange-50 text-orange-600
                            @elseif($priority === 'medium') bg-yellow-50 text-yellow-600
                            @else bg-green-50 text-green-600
                            @endif">
                            <i class="fas fa-flag text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ $priority }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">{{ number_format($count) }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? number_format(($count / $stats['total']) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <i class="fas fa-chart-bar text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada data prioritas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

