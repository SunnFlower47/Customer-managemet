@extends('layouts.app')

@section('title', 'Dashboard MikroTik')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-server"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-indigo-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $mikrotik->nama }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ $mikrotik->ip_address }}:{{ $mikrotik->port }}</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <form action="{{ route('mikrotiks.test-connection', $mikrotik) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-plug mr-2 text-xs sm:text-sm"></i>Test Koneksi
                </button>
            </form>
            @can('manage-mikrotik')
            <a href="{{ route('mikrotiks.edit', $mikrotik) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            @endcan
            <a href="{{ route('mikrotiks.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-network-wired text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">STATUS</p>
                    @if($mikrotik->connection_status === 'online')
                    <p class="text-lg font-bold text-green-600">Online</p>
                    @elseif($mikrotik->connection_status === 'offline')
                    <p class="text-lg font-bold text-gray-600">Offline</p>
                    @else
                    <p class="text-lg font-bold text-red-600">Error</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-green-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">PPPoE AKTIF</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($activePppoeCount) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-microchip text-purple-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">ROUTEROS</p>
                    <p class="text-lg font-bold text-gray-900">{{ strtoupper($mikrotik->routeros_version) }}</p>
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-amber-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">LAST CONNECTED</p>
                    @if($mikrotik->last_connected_at)
                    <p class="text-xs font-bold text-gray-900">{{ $mikrotik->last_connected_at->diffForHumans() }}</p>
                    @else
                    <p class="text-xs text-gray-400">Belum pernah</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PPPoE Search -->
    <div class="app-card space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Cari PPPoE</h2>
            <p class="text-sm text-gray-500">Cari username PPPoE di router ini</p>
        </div>
        <form action="{{ route('mikrotiks.search-pppoe', $mikrotik) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text"
                   name="username"
                   value="{{ old('username') }}"
                   required
                   class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Masukkan username PPPoE">
            <button type="submit"
                    class="px-6 py-3 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>

        @if(session('pppoe_found'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <h3 class="text-sm font-semibold text-green-900 mb-2">PPPoE Ditemukan</h3>
            <pre class="text-xs text-gray-700 overflow-x-auto">{{ json_encode(session('pppoe_found'), JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>

    <!-- Resource Usage (if available) -->
    @if(!empty($resourceUsage))
    <div class="app-card space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Resource Usage</h2>
            <p class="text-sm text-gray-500">Penggunaan resource router</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">CPU Load</p>
                <p class="text-lg font-bold text-gray-900">{{ $resourceUsage['cpu-load'] ?? 'N/A' }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Memory Usage</p>
                <p class="text-lg font-bold text-gray-900">{{ $resourceUsage['free-memory'] ?? 'N/A' }} / {{ $resourceUsage['total-memory'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Uptime</p>
                <p class="text-lg font-bold text-gray-900">{{ $resourceUsage['uptime'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

