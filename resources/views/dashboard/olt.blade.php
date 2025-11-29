@extends('layouts.app')

@section('title', 'OLT Dashboard')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">OLT Dashboard</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Ringkasan kesehatan jaringan OLT dan ONU</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('olts.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-server mr-2"></i>Daftar OLT
            </a>
            <a href="{{ route('onus.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-sitemap mr-2"></i>All ONUs
            </a>
            @can('manage-onu')
            <a href="{{ route('onus.register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Register ONU
            </a>
            @endcan
        </div>
    </div>

    <!-- OLT Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total OLT</p>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-server text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_olts'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Perangkat terdaftar</p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">OLT Online</p>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['online_olts'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                @if($stats['total_olts'] > 0)
                    {{ number_format(($stats['online_olts'] / $stats['total_olts']) * 100, 1) }}% uptime
                @else
                    Tidak ada data
                @endif
            </p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">OLT Offline</p>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['offline_olts'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Butuh pengecekan</p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Port Tersedia</p>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plug text-purple-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['available_ports'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Dari {{ $stats['total_ports'] }} total port</p>
        </div>
    </div>

    <!-- ONU Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total ONU</p>
                <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sitemap text-teal-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_onus'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Perangkat terhubung</p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">ONU Online</p>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wifi text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['online_onus'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                @if($stats['total_onus'] > 0)
                    {{ number_format(($stats['online_onus'] / $stats['total_onus']) * 100, 1) }}% aktif
                @else
                    Tidak ada data
                @endif
            </p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">ONU Offline</p>
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-power-off text-gray-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-600">{{ $stats['offline_onus'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Tidak terhubung</p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Loss of Signal</p>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['los_onus'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Butuh perhatian</p>
        </div>
    </div>

    <!-- Signal Quality Statistics -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Kualitas Signal ONU</h2>
            <a href="{{ route('onus.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-green-700 uppercase">Good Signal</p>
                    <i class="fas fa-signal text-green-600"></i>
                </div>
                <p class="text-2xl font-bold text-green-700">{{ $stats['signal_good'] }}</p>
                <p class="text-xs text-green-600 mt-1">≥ -26.00 dBm</p>
                @if($stats['online_onus'] > 0)
                    <p class="text-xs text-green-600 mt-1">{{ number_format(($stats['signal_good'] / $stats['online_onus']) * 100, 1) }}% dari ONU online</p>
                @endif
            </div>
            
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-orange-700 uppercase">Warning</p>
                    <i class="fas fa-exclamation-circle text-orange-600"></i>
                </div>
                <p class="text-2xl font-bold text-orange-700">{{ $stats['signal_warning'] }}</p>
                <p class="text-xs text-orange-600 mt-1">-28.00 s/d -26.00 dBm</p>
                @if($stats['online_onus'] > 0)
                    <p class="text-xs text-orange-600 mt-1">{{ number_format(($stats['signal_warning'] / $stats['online_onus']) * 100, 1) }}% dari ONU online</p>
                @endif
            </div>
            
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-red-700 uppercase">Critical</p>
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <p class="text-2xl font-bold text-red-700">{{ $stats['signal_critical'] }}</p>
                <p class="text-xs text-red-600 mt-1">&lt; -28.00 dBm</p>
                @if($stats['online_onus'] > 0)
                    <p class="text-xs text-red-600 mt-1">{{ number_format(($stats['signal_critical'] / $stats['online_onus']) * 100, 1) }}% dari ONU online</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- OLT Status Timeline -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Status OLT Terbaru</h2>
                <span class="text-xs text-gray-400">Terakhir diperbarui</span>
            </div>
            <div class="space-y-4">
                @forelse($olts as $olt)
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <div class="relative flex-shrink-0 mt-1">
                        <span class="w-3 h-3 rounded-full {{ $olt->status === 'online' ? 'bg-green-500' : 'bg-red-500' }} block"></span>
                        @if(!$loop->last)
                            <span class="absolute left-1/2 top-3 h-full w-px bg-gray-200 -translate-x-1/2"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $olt->nama }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="font-mono">{{ $olt->ip_address }}</span>
                                    @if($olt->onus_count > 0)
                                        <span class="mx-2">·</span>
                                        <span>{{ $olt->onus_count }} ONU</span>
                                    @endif
                                    @if($olt->pon_ports_count > 0)
                                        <span class="mx-2">·</span>
                                        <span>{{ $olt->pon_ports_count }} Port</span>
                                    @endif
                                </p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $olt->status === 'online' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($olt->status ?? 'unknown') }}
                            </span>
                        </div>
                        @if($olt->last_checked_at)
                            <p class="text-xs text-gray-400 mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                Terakhir sync: {{ $olt->last_checked_at->diffForHumans() }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                Belum pernah sync
                            </p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-server text-gray-300 text-4xl mb-3"></i>
                    <p class="text-sm text-gray-400">Belum ada data OLT</p>
                    <a href="{{ route('olts.create') }}" class="text-xs text-blue-600 hover:text-blue-700 mt-2 inline-block">Tambah OLT pertama</a>
                </div>
                @endforelse
            </div>
            @if($olts->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('olts.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua OLT <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif
        </div>

        <!-- Sidebar: Recent ONU Activities -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Aktivitas ONU</h2>
                    <a href="{{ route('onus.index') }}" class="text-xs text-blue-600 hover:text-blue-700">Lihat Semua</a>
                </div>
                <div class="space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($recentOnus as $onu)
                    <div class="p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">
                                    {{ $onu->nama ?? $onu->serial_number }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1 truncate">
                                    <span class="font-mono">{{ $onu->serial_number }}</span>
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-xs font-medium flex-shrink-0 {{ $onu->status === 'online' ? 'bg-green-100 text-green-700' : ($onu->status === 'offline' ? 'bg-gray-100 text-gray-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($onu->status ?? 'unknown') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            <i class="fas fa-server mr-1"></i>{{ $onu->olt?->nama ?? '-' }}
                            @if($onu->last_online_at)
                                <span class="mx-1">·</span>
                                <i class="fas fa-clock mr-1"></i>{{ $onu->last_online_at->diffForHumans() }}
                            @endif
                        </p>
                        @if($onu->rx_power)
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-signal mr-1"></i>RX: {{ number_format($onu->rx_power, 2) }} dBm
                            </p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-sitemap text-gray-300 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-400">Tidak ada aktivitas terbaru</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
