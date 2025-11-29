@extends('layouts.app')

@section('title', 'Daftar OLT')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-server"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Daftar OLT</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola dan monitor Optical Line Terminal (OLT)</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('manage-olt')
            <a href="{{ route('olts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah OLT
            </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total OLT</p>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-server text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Perangkat terdaftar</p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Online</p>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['online'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                @if($stats['total'] > 0)
                    {{ number_format(($stats['online'] / $stats['total']) * 100, 1) }}% uptime
                @else
                    Tidak ada data
                @endif
            </p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Offline</p>
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-gray-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-600">{{ $stats['offline'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Butuh pengecekan</p>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Error</p>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['error'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Butuh perhatian</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <form method="GET" action="{{ route('olts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Kode, nama, atau IP"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Status
                </label>
                <select name="status"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Error</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag mr-1"></i>Vendor
                </label>
                <select name="vendor"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Vendor</option>
                    @foreach($supportedVendors as $vendor => $info)
                    <option value="{{ $vendor }}" {{ request('vendor') === $vendor ? 'selected' : '' }}>{{ strtoupper($vendor) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('olts.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- OLT Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-500 to-indigo-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Kode OLT</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Vendor/Model</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">ONU/Port</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Last Check</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($olts as $olt)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $olt->kode_olt }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $olt->nama }}</p>
                            @if($olt->alamat)
                            <p class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($olt->alamat, 40) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-mono text-gray-900">{{ $olt->ip_address }}</p>
                            @if($olt->port)
                            <p class="text-xs text-gray-500">Port: {{ $olt->port }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                {{ $olt->vendor ?? 'N/A' }} / {{ $olt->model ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($olt->status === 'online')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-circle text-[6px] mr-1.5"></i>Online
                            </span>
                            @elseif($olt->status === 'offline')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                <i class="fas fa-circle text-[6px] mr-1.5"></i>Offline
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                <i class="fas fa-circle text-[6px] mr-1.5"></i>Error
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $olt->onus_count ?? $olt->onu_terhubung ?? 0 }} ONU</p>
                            <p class="text-xs text-gray-500">
                                {{ $olt->ports_terpakai ?? 0 }}/{{ $olt->total_ports ?? 0 }} Port
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            @if($olt->last_checked_at)
                            <p class="text-xs text-gray-600">{{ $olt->last_checked_at->diffForHumans() }}</p>
                            @else
                            <p class="text-xs text-gray-400">Belum pernah</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('olts.show', $olt) }}"
                                   class="px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('manage-olt')
                                <a href="{{ route('olts.edit', $olt) }}"
                                   class="px-3 py-1.5 text-xs font-semibold bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('olts.destroy', $olt) }}" method="POST" class="inline delete-form"
                                      data-message="Yakin ingin menghapus OLT ini? Semua ONU yang terhubung akan terpengaruh.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-server text-5xl mb-3"></i>
                                <p class="text-sm font-medium">Tidak ada OLT ditemukan</p>
                                <p class="text-xs mt-1">Mulai dengan menambahkan OLT pertama</p>
                                @can('manage-olt')
                                <a href="{{ route('olts.create') }}" class="inline-block mt-3 px-4 py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-plus mr-2"></i>Tambah OLT
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-4 p-4">
            @forelse($olts as $olt)
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $olt->kode_olt }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $olt->nama }}</p>
                    </div>
                    @if($olt->status === 'online')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800 flex-shrink-0 ml-2">Online</span>
                    @elseif($olt->status === 'offline')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-800 flex-shrink-0 ml-2">Offline</span>
                    @else
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-800 flex-shrink-0 ml-2">Error</span>
                    @endif
                </div>
                <div class="space-y-2 text-xs text-gray-600 mb-4">
                    <p class="flex items-center">
                        <i class="fas fa-network-wired w-4 mr-2"></i>
                        <span class="font-mono">{{ $olt->ip_address }}</span>
                        @if($olt->port)
                        <span class="ml-2">:{{ $olt->port }}</span>
                        @endif
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-server w-4 mr-2"></i>
                        <span>{{ $olt->vendor ?? 'N/A' }} / {{ $olt->model ?? 'N/A' }}</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-plug w-4 mr-2"></i>
                        <span>{{ $olt->onus_count ?? $olt->onu_terhubung ?? 0 }} ONU, {{ $olt->ports_terpakai ?? 0 }}/{{ $olt->total_ports ?? 0 }} Port</span>
                    </p>
                    @if($olt->last_checked_at)
                    <p class="flex items-center">
                        <i class="fas fa-clock w-4 mr-2"></i>
                        <span>Terakhir: {{ $olt->last_checked_at->diffForHumans() }}</span>
                    </p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('olts.show', $olt) }}" class="flex-1 px-3 py-2 text-xs font-semibold bg-blue-50 text-blue-600 rounded-lg text-center hover:bg-blue-100 transition">Detail</a>
                    @can('manage-olt')
                    <a href="{{ route('olts.edit', $olt) }}" class="flex-1 px-3 py-2 text-xs font-semibold bg-yellow-50 text-yellow-600 rounded-lg text-center hover:bg-yellow-100 transition">Edit</a>
                    @endcan
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-server text-5xl mb-3"></i>
                <p class="text-sm font-medium">Tidak ada OLT ditemukan</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($olts->hasPages())
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $olts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
