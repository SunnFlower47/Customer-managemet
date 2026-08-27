@extends('layouts.app')

@section('title', 'Detail MikroTik')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-server"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $mikrotik->nama }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs sm:text-sm text-gray-600">{{ $mikrotik->ip_address }}:{{ $mikrotik->port }}</span>
                    @if($mikrotik->connection_status == 'online')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>Online
                        </span>
                    @elseif($mikrotik->connection_status == 'error')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800" title="{{ $mikrotik->last_error ?? 'Koneksi gagal' }}">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>Offline / Error
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-amber-500 rounded-full"></span>Belum Dites
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="page-header__actions flex flex-wrap gap-2">
            <a href="{{ route('mikrotik.index') }}" 
               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-300 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <form action="{{ route('mikrotik.sync', $mikrotik->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-sm"
                        onclick="return confirm('Mulai sinkronisasi?')">
                    <i class="fas fa-sync mr-2"></i>Sync User
                </button>
            </form>
            <a href="{{ route('mikrotik.edit', $mikrotik->id) }}" 
               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('mikrotik.destroy', $mikrotik->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus router MikroTik ini? Semua data PPPoE terkait akan dihapus.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-red-600 text-white rounded-xl hover:bg-red-700 transition shadow-sm">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Review -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="app-card p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-500 font-medium text-sm">Total User PPPoE</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $mikrotik->pppoeUsers->count() }}</h3>
            </div>
            <div class="h-12 w-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 text-xl">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="app-card p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-500 font-medium text-sm">User Terhubung</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $mikrotik->pppoeUsers->whereNotNull('pelanggan_id')->count() }}</h3>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 text-xl">
                <i class="fas fa-link"></i>
            </div>
        </div>
        <div class="app-card p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-500 font-medium text-sm">Belum Terhubung</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $mikrotik->pppoeUsers->whereNull('pelanggan_id')->count() }}</h3>
            </div>
            <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 text-xl">
                <i class="fas fa-unlink"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Data PPPoE</p>
                <h2 class="text-base font-semibold text-gray-900">Daftar User PPPoE Tersinkronisasi</h2>
            </div>
            <a href="{{ route('mikrotik.unmapped', $mikrotik->id) }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">
                Lihat yang belum terhubung <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Username
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Profile
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-network-wired mr-2"></i>IP Address
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user-check mr-2"></i>Pelanggan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-indigo-50 transition border-b border-gray-100">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-900">{{ $user->username }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $user->profile }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-gray-600">
                            {{ $user->remote_address ?? '-' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($user->pelanggan)
                                <a href="{{ route('pelanggans.show', $user->pelanggan_id) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    {{ $user->pelanggan->nama }}
                                </a>
                            @else
                                <a href="{{ route('mikrotik.create-customer', $user->id) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 hover:bg-orange-200">
                                    <i class="fas fa-plus mr-1.5"></i>
                                    Buat Pelanggan
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <p>Belum ada data user PPPoE.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Mobile Layout -->
        <div class="lg:hidden space-y-3">
            @forelse($users as $user)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-gray-900">{{ $user->username }}</span>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-lg text-gray-600">{{ $user->profile }}</span>
                </div>
                <div class="text-xs text-gray-500 mb-3 font-mono">
                    IP: {{ $user->remote_address ?? '-' }}
                </div>
                
                @if($user->pelanggan)
                    <div class="bg-blue-50 rounded-lg p-2 flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-600"></i>
                        <span class="text-xs font-semibold text-blue-800">{{ $user->pelanggan->nama }}</span>
                    </div>
                @else
                     <a href="{{ route('mikrotik.create-customer', $user->id) }}" class="block w-full text-center py-2 rounded-lg bg-orange-50 text-orange-700 font-semibold text-xs border border-orange-200 hover:bg-orange-100">
                        <i class="fas fa-plus mr-1"></i> Buat Pelanggan
                    </a>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <p>Belum ada user.</p>
            </div>
            @endforelse
            
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
