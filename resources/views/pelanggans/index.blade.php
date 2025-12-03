@extends('layouts.app')

@section('title', 'Pelanggan - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg relative">
                <i class="fas fa-users text-white text-xl sm:text-2xl"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-orange-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Data Pelanggan</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola dan pantau data pelanggan WiFi Anda</p>
            </div>
        </div>
        <div class="page-header__actions">
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-700">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <span class="font-medium">Kelola data pelanggan WiFi</span>
                </div>
            </div>
            <a href="{{ route('pelanggans.create', request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id'])) }}"
               class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                <span>Tambah Pelanggan</span>
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-search mr-2 text-orange-600"></i>Pencarian & Filter
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-orange-500"></i>
                Gunakan filter untuk menemukan pelanggan dengan mudah
            </p>
        </div>
        <form method="GET" action="{{ route('pelanggans.index') }}" class="space-y-6">
            <!-- Search -->
            <div class="col-span-1 md:col-span-2">
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-orange-600"></i>Cari Pelanggan
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Cari berdasarkan nama, PPPoE, serial STB, no HP, atau alamat..."
                        class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <div id="search_suggestions"
                         class="absolute z-20 mt-2 w-full bg-white border border-gray-100 rounded-2xl shadow-lg hidden max-h-80 overflow-y-auto">
                        <div class="py-3 text-center text-xs text-gray-400">Tidak ada hasil</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 col-span-1 md:col-span-2">
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Status
                    </label>
                    <select name="status" id="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="isolir" {{ request('status') === 'isolir' ? 'selected' : '' }}>Isolir</option>
                        <option value="bayar double" {{ request('status') === 'bayar double' ? 'selected' : '' }}>Bayar Double</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label for="paket_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box mr-2 text-blue-600"></i>Paket
                    </label>
                    <select name="paket_id" id="paket_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Paket</option>
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->id }}" {{ request('paket_id') == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }} - Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="penagih_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tie mr-2 text-blue-600"></i>Penagih
                    </label>
                    <div class="relative">
                        <input type="text"
                            id="penagih_search"
                            class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                            placeholder="Cari penagih..."
                            autocomplete="off">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user-tie text-gray-400"></i>
                        </div>
                        <input type="hidden" name="penagih_id" id="penagih_id" value="{{ request('penagih_id') }}">
                        <div id="penagih_dropdown" class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-xl hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-3 text-gray-500 cursor-pointer hover:bg-gray-100 font-medium" data-value="">
                                Semua Penagih
                            </div>
                            @foreach($penagihs as $penagih)
                                <div class="px-4 py-3 cursor-pointer hover:bg-gray-100 font-medium" data-value="{{ $penagih->id }}" data-name="{{ $penagih->nama }}">
                                    {{ $penagih->nama }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div>
                    <label for="odp_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>ODP
                    </label>
                    <select name="odp_id" id="odp_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua ODP</option>
                        @foreach(\App\Models\Odp::where('status', 'aktif')->get() as $odp)
                            <option value="{{ $odp->id }}" {{ request('odp_id') == $odp->id ? 'selected' : '' }}>
                                {{ $odp->nama }} ({{ $odp->koordinat_latitude }}, {{ $odp->koordinat_longitude }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Advanced Filters (Collapsible) -->
            <div x-data="{ open: {{ request()->has(['date_from', 'date_to', 'created_from', 'created_to']) ? 'true' : 'false' }} }" class="space-y-4">
                <button type="button" @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-gray-900 transition">
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    <span>Filter Lanjutan</span>
                </button>
                <div x-show="open" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 border-t border-gray-200 pt-4">
                    <div>
                        <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-blue-600"></i>Tanggal Mulai Dari
                        </label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2 text-blue-600"></i>Tanggal Mulai Sampai
                        </label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    </div>
                    <div>
                        <label for="created_from" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-plus mr-2 text-blue-600"></i>Dibuat Dari
                        </label>
                        <input type="date" name="created_from" id="created_from" value="{{ request('created_from') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    </div>
                    <div>
                        <label for="created_to" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-2 text-blue-600"></i>Dibuat Sampai
                        </label>
                        <input type="date" name="created_to" id="created_to" value="{{ request('created_to') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    </div>
                </div>
            </div>

            <div class="inline-actions w-full col-span-1 md:col-span-4 flex flex-col sm:flex-row gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 text-sm font-semibold min-w-[140px] text-center">
                    <i class="fas fa-filter mr-2"></i>Filter Data
                </button>
                <a href="{{ route('pelanggans.index') }}" class="flex-1 bg-white border-2 border-gray-300 text-gray-700 px-5 py-3 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center text-sm font-semibold transition-all duration-200 min-w-[140px]">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </a>
                <a href="{{ route('pelanggans.export.pdf', request()->query()) }}" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-5 py-3 rounded-xl hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-center text-sm font-semibold transition-all duration-200 min-w-[140px]">
                    <i class="fas fa-download mr-2"></i>Export PDF
                </a>
            </div>
        </form>
    </div>
    <!-- Data Table -->
    <div class="mt-8 app-card app-card--soft overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Nama
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-wifi mr-2"></i>PPPoE
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-map-marker-alt mr-2"></i>Alamat
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-box mr-2"></i>Paket
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user-tie mr-2"></i>Penagih
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Tanggal Pembayaran
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pelanggans as $pelanggan)
                    <tr class="hover:bg-blue-50 transition-all duration-200 border-b border-gray-100">
                        <td class="px-4 py-5 sm:px-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg relative">
                                        <div class="absolute -top-1 -right-1 h-4 w-4 bg-orange-500 rounded-full border-2 border-white"></div>
                                        <span class="text-white font-bold text-lg">{{ substr($pelanggan->nama, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $pelanggan->nama }}</div>
                                    <div class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-phone mr-1"></i>{{ $pelanggan->no_hp }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-5 sm:px-5 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-mono bg-gray-100 px-3 py-2 rounded-lg">{{ $pelanggan->pppoe }}</div>
                        </td>
                        <td class="px-4 py-5 sm:px-5">
                            <div class="text-sm text-gray-900 max-w-xs truncate bg-gray-50 px-3 py-2 rounded-lg" title="{{ $pelanggan->alamat }}">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $pelanggan->alamat }}
                            </div>
                        </td>
                        <td class="px-4 py-5 sm:px-5 whitespace-nowrap">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-3 rounded-xl border border-green-200">
                                <div class="text-sm font-bold text-green-900">{{ $pelanggan->paket->nama_paket }}</div>
                                <div class="text-sm text-green-700 font-semibold">Rp {{ number_format((float)$pelanggan->paket->harga, 0, ',', '.') }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-5 sm:px-5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($pelanggan->penagih)
                                    <div class="flex items-center bg-blue-50 px-3 py-2 rounded-lg">
                                        <i class="fas fa-user-tie mr-2 text-blue-600"></i>
                                        <span class="font-medium">{{ $pelanggan->penagih->nama }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center bg-gray-100 px-3 py-2 rounded-lg">
                                        <i class="fas fa-user-slash mr-2 text-gray-400"></i>
                                        <span class="text-gray-500 italic font-medium">Belum ada penagih</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-5 sm:px-5 whitespace-nowrap">
                            <div class="text-sm text-gray-900 bg-yellow-50 px-3 py-2 rounded-lg">
                                <i class="fas fa-calendar mr-1 text-yellow-600"></i>Tanggal {{ $pelanggan->tanggal_pembayaran }}
                            </div>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-bold
                                {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' :
                                ($pelanggan->status === 'bayar double' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
                                ($pelanggan->status === 'nonaktif' ? 'bg-gray-100 text-gray-800 border border-gray-200' : 'bg-red-100 text-red-800 border border-red-200')) }}">
                                <i class="fas fa-circle mr-1 text-xs"></i>{{ ucfirst($pelanggan->status) }}
                            </span>
                        </td>
                                    <td class="px-4 py-5 sm:px-5 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="inline-actions justify-center">
                                            <!-- Tombol Detail -->
                                            <a href="{{ route('pelanggans.show', $pelanggan) }}"
                                            class="inline-flex items-center px-3.5 py-2.5 text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200"
                                            title="Lihat Detail">
                                                <i class="fas fa-eye mr-2"></i>Detail
                                            </a>

                                            <!-- Tombol Edit -->
                                            <a href="{{ route('pelanggans.edit', array_merge([$pelanggan], request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id']))) }}"
                                            class="inline-flex items-center px-3.5 py-2.5 text-sm font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200"
                                            title="Edit Data">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </a>

                                            <!-- Tombol Hapus -->
                                            <form method="POST" action="{{ route('pelanggans.destroy', $pelanggan) }}" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                @if(request('page'))
                                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                                @endif
                                                @if(request('search'))
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                @endif
                                                @if(request('status'))
                                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                                @endif
                                                @if(request('penagih_id'))
                                                    <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
                                                @endif
                                                @if(request('paket_id'))
                                                    <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
                                                @endif
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-3.5 py-2.5 text-sm font-semibold bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200"
                                                        title="Hapus Data">
                                                    <i class="fas fa-trash mr-2"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <x-empty-state
                                            icon="fas fa-users"
                                            title="Tidak ada pelanggan"
                                            description="Belum ada data pelanggan yang ditemukan. Mulai dengan menambahkan pelanggan pertama Anda."
                                            action-route="{{ route('pelanggans.create') }}"
                                            action-text="Tambah Pelanggan Pertama"
                                        />
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="lg:hidden space-y-3.5">
                        @forelse($pelanggans as $pelanggan)
                        <div class="mobile-card hover:shadow-xl transition-all duration-200">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white font-semibold text-base">
                                    {{ substr($pelanggan->nama, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $pelanggan->nama }}</p>
                                            <p class="text-xs text-gray-500 flex items-center gap-1.5">
                                                <i class="fas fa-phone"></i>{{ $pelanggan->no_hp }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                            @if($pelanggan->status === 'aktif') bg-emerald-50 text-emerald-600
                                            @elseif($pelanggan->status === 'isolir') bg-rose-50 text-rose-600
                                            @elseif($pelanggan->status === 'bayar double') bg-amber-50 text-amber-600
                                            @elseif($pelanggan->status === 'nonaktif') bg-gray-50 text-gray-600
                                            @else bg-amber-50 text-amber-600 @endif">
                                            <i class="fas fa-circle mr-1 text-[7px]"></i>{{ ucfirst($pelanggan->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5">
                                    <p class="text-[10px] font-semibold text-gray-500 mb-1">PPPoE</p>
                                    <p class="font-mono text-xs break-all">{{ \Illuminate\Support\Str::limit($pelanggan->pppoe, 24) }}</p>
                                </div>
                                <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2.5">
                                    <p class="text-[10px] font-semibold text-emerald-600 mb-1">Paket</p>
                                    <p class="text-xs font-semibold text-emerald-800">{{ $pelanggan->paket->nama_paket }}</p>
                                    <p class="text-[11px] text-emerald-600">Rp {{ number_format((float)$pelanggan->paket->harga, 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-2.5">
                                    <p class="text-[10px] font-semibold text-yellow-600 mb-1">Tanggal Bayar</p>
                                    <p class="text-xs font-semibold text-yellow-900">Tanggal {{ $pelanggan->tanggal_pembayaran }}</p>
                                </div>
                                <div class="col-span-2 bg-gray-50 border border-gray-100 rounded-lg p-2.5">
                                    <p class="text-[10px] font-semibold text-gray-500 mb-1">Alamat</p>
                                    <p class="text-xs">{{ $pelanggan->alamat }}</p>
                                </div>
                                <div class="col-span-2 bg-blue-50 border border-blue-100 rounded-lg p-2.5">
                                    <p class="text-[10px] font-semibold text-blue-600 mb-1">Penagih</p>
                                    <p class="text-xs font-semibold text-blue-900">{{ $pelanggan->penagih ? $pelanggan->penagih->nama : 'Belum ditugaskan' }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-1.5">
                                <a href="{{ route('pelanggans.show', $pelanggan) }}"
                                   class="inline-flex flex-col items-center justify-center px-2 py-1.5 text-[10px] font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-md hover:scale-105 transition-all duration-200 text-center">
                                    <i class="fas fa-eye mb-0.5 text-[10px]"></i>Detail
                                </a>
                                <a href="{{ route('pelanggans.edit', array_merge([$pelanggan], request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id']))) }}"
                                   class="inline-flex flex-col items-center justify-center px-2 py-1.5 text-[10px] font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg hover:shadow-md hover:scale-105 transition-all duration-200 text-center">
                                    <i class="fas fa-edit mb-0.5 text-[10px]"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('pelanggans.destroy', $pelanggan) }}" class="delete-form w-full">
                                    @csrf
                                    @method('DELETE')
                                    @if(request('page'))
                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                    @endif
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                    @if(request('status'))
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif
                                    @if(request('penagih_id'))
                                        <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
                                    @endif
                                    @if(request('paket_id'))
                                        <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
                                    @endif
                                    <button type="submit"
                                            class="inline-flex flex-col items-center justify-center px-2 py-1.5 text-[10px] font-semibold bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:shadow-md hover:scale-105 transition-all duration-200 w-full text-center">
                                        <i class="fas fa-trash mb-0.5 text-[10px]"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <x-empty-state
                            icon="fas fa-users"
                            title="Tidak ada pelanggan"
                            description="Belum ada data pelanggan yang ditemukan. Mulai dengan menambahkan pelanggan pertama Anda."
                            action-route="{{ route('pelanggans.create') }}"
                            action-text="Tambah Pelanggan Pertama"
                        />
                        @endforelse
                    </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 app-card">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-xs sm:text-sm text-gray-600">
                Menampilkan <span class="font-semibold text-gray-900">{{ $pelanggans->firstItem() ?? 0 }}</span> -
                <span class="font-semibold text-gray-900">{{ $pelanggans->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-900">{{ $pelanggans->total() }}</span> pelanggan
            </div>
            <div class="flex items-center justify-center sm:justify-end">
                {{ $pelanggans->appends(request()->query())->onEachSide(1)->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>

    @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Pelanggans page loaded, initializing SweetAlert...');

                // Helper debounce
                const debounce = (fn, delay = 300) => {
                    let timeout;
                    return (...args) => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => fn.apply(this, args), delay);
                    };
                };

                // Realtime pelanggan suggestions
                const pelangganSearchInput = document.getElementById('search');
                const suggestionPanel = document.getElementById('search_suggestions');
                const suggestionUrl = "{{ route('pelanggans.suggestions') }}";
                let activeSuggestionAbort;

                function hideSuggestions() {
                    if (suggestionPanel) {
                        suggestionPanel.classList.add('hidden');
                        suggestionPanel.dataset.hasResults = 'false';
                    }
                }

                function renderSuggestions(items) {
                    if (!suggestionPanel) return;
                    if (!items.length) {
                        suggestionPanel.innerHTML = '<div class="py-4 text-center text-xs text-gray-400">Tidak ditemukan pelanggan</div>';
                        suggestionPanel.dataset.hasResults = 'false';
                        suggestionPanel.classList.remove('hidden');
                        return;
                    }

                    suggestionPanel.innerHTML = items.map(item => `
                        <button type="button"
                                class="w-full text-left px-4 py-3 flex flex-col gap-1 hover:bg-gray-50 focus:bg-gray-50 transition rounded-2xl"
                                data-url="${item.detail_url}"
                                data-name="${item.nama}">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-sm text-gray-900 truncate">${item.nama}</span>
                                <span class="text-[11px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">${item.pppoe || '—'}</span>
                            </div>
                            <div class="text-[11px] text-gray-500 truncate">
                                ${item.no_hp ? `<i class="fas fa-phone mr-1 text-[10px]"></i>${item.no_hp}` : ''}
                                ${item.penagih ? `<span class="ml-2"><i class="fas fa-user-tie mr-1 text-[10px]"></i>${item.penagih}</span>` : ''}
                            </div>
                        </button>
                    `).join('');

                    suggestionPanel.dataset.hasResults = 'true';
                    suggestionPanel.classList.remove('hidden');
                }

                async function fetchSuggestions(term) {
                    if (!suggestionPanel) return;

                    if (activeSuggestionAbort) {
                        activeSuggestionAbort.abort();
                    }
                    activeSuggestionAbort = new AbortController();

                    try {
                        const response = await fetch(`${suggestionUrl}?q=${encodeURIComponent(term)}`, {
                            headers: {
                                'Accept': 'application/json'
                            },
                            signal: activeSuggestionAbort.signal
                        });
                        if (!response.ok) {
                            throw new Error('Gagal memuat saran');
                        }
                        const data = await response.json();
                        renderSuggestions(data.data || []);
                    } catch (error) {
                        if (error.name === 'AbortError') return;
                        console.error(error);
                        suggestionPanel.innerHTML = '<div class="py-4 text-center text-xs text-red-400">Gagal memuat saran</div>';
                        suggestionPanel.classList.remove('hidden');
                    }
                }

                if (pelangganSearchInput && suggestionPanel) {
                    pelangganSearchInput.setAttribute('autocomplete', 'off');

                    const debouncedFetch = debounce((term) => {
                        if (term.length < 2) {
                            hideSuggestions();
                            return;
                        }
                        fetchSuggestions(term);
                    }, 350);

                    pelangganSearchInput.addEventListener('input', (event) => {
                        debouncedFetch(event.target.value.trim());
                    });

                    pelangganSearchInput.addEventListener('focus', () => {
                        if (suggestionPanel.dataset.hasResults === 'true') {
                            suggestionPanel.classList.remove('hidden');
                        }
                    });

                    suggestionPanel.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                    });

                    suggestionPanel.addEventListener('click', (e) => {
                        const item = e.target.closest('button[data-url]');
                        if (!item) return;
                        pelangganSearchInput.value = item.dataset.name;
                        hideSuggestions();
                        window.location.href = item.dataset.url;
                    });

                    document.addEventListener('click', (event) => {
                        if (!suggestionPanel.contains(event.target) && event.target !== pelangganSearchInput) {
                            hideSuggestions();
                        }
                    });

                    pelangganSearchInput.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            hideSuggestions();
                            pelangganSearchInput.blur();
                        }
                    });
                }

                // Searchable Penagih Dropdown
                const penagihSearchInput = document.getElementById('penagih_search');
                const hiddenInput = document.getElementById('penagih_id');
                const dropdown = document.getElementById('penagih_dropdown');

                if (penagihSearchInput && hiddenInput && dropdown) {
                    const allOptions = dropdown.querySelectorAll('[data-value]');
                    let clickTimeout = null;

                    // Set initial value if selected
                    const selectedValue = hiddenInput.value;
                    if (selectedValue) {
                        const selectedOption = dropdown.querySelector(`[data-value="${selectedValue}"]`);
                        if (selectedOption) {
                            penagihSearchInput.value = selectedOption.dataset.name || selectedOption.textContent.trim();
                        }
                    }

                    // Show dropdown
                    function showDropdown() {
                        dropdown.classList.remove('hidden');
                        filterOptions();
                    }

                    // Hide dropdown
                    function hideDropdown() {
                        dropdown.classList.add('hidden');
                    }

                    // Filter options based on search
                    function filterOptions() {
                        const searchTerm = penagihSearchInput.value.toLowerCase().trim();
                        allOptions.forEach(option => {
                            const text = option.textContent.toLowerCase().trim();
                            option.style.display = text.includes(searchTerm) ? 'block' : 'none';
                        });
                    }

                    penagihSearchInput.addEventListener('focus', showDropdown);

                    penagihSearchInput.addEventListener('blur', function() {
                        clickTimeout = setTimeout(() => {
                            hideDropdown();
                        }, 300);
                    });

                    penagihSearchInput.addEventListener('input', showDropdown);

                    dropdown.addEventListener('mousedown', function(e) {
                        e.preventDefault(); // Prevent input blur
                    });

                    dropdown.addEventListener('click', function(e) {
                        const option = e.target.closest('[data-value]');
                        if (option) {
                            const value = option.dataset.value;
                            const name = option.dataset.name || option.textContent.trim();

                            if (clickTimeout) {
                                clearTimeout(clickTimeout);
                            }

                            hiddenInput.value = value;
                            penagihSearchInput.value = name;
                            hideDropdown();

                            console.log('Penagih selected:', name, 'ID:', value);
                        }
                    });

                    penagihSearchInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            hideDropdown();
                        }
                    });
                }

                // Handle delete confirmation with SweetAlert
                const deleteForms = document.querySelectorAll('.delete-form');
                console.log('Found delete forms:', deleteForms.length);

                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Delete form submitted');

                        const form = this;
                        // Get pelanggan name from both desktop and mobile views
                        let pelangganName = '';
                        const tableRow = form.closest('tr');
                        const mobileCard = form.closest('.border-b');

                        if (tableRow) {
                            // Desktop view
                            pelangganName = tableRow.querySelector('td:first-child').textContent.trim();
                        } else if (mobileCard) {
                            // Mobile view
                            pelangganName = mobileCard.querySelector('.text-sm.font-medium.text-gray-900').textContent.trim();
                        }
                        console.log('Pelanggan to delete:', pelangganName);

                        Swal.fire({
                            title: 'Hapus Pelanggan?',
                            text: `Apakah Anda yakin ingin menghapus pelanggan "${pelangganName}"?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                console.log('User confirmed deletion');
                                form.submit();
                            } else {
                                console.log('User cancelled deletion');
                            }
                        });
                    });
                });

            });
            </script>

            @if(session('success'))
            <script>
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#10B981'
            });
            </script>
            @endif

            @if(session('error'))
            <script>
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
            </script>
            @endif
            @endpush
            @endsection

