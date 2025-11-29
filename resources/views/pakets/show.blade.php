@extends('layouts.app')

@section('title', 'Detail Paket - WiFi Billing Management')

@section('content')
@php
    $totalCustomers = $paket->pelanggans()->count();
    $activeCustomers = $paket->pelanggans()->where('status', 'aktif')->count();
    $estimatedRevenue = $activeCustomers * $paket->harga;
@endphp

<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-box"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $paket->nama_paket }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Detail paket internet dan pelanggan yang menggunakan</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-blue-50 text-blue-600 border border-blue-100 rounded-xl">
                Rp {{ number_format((float)$paket->harga, 0, ',', '.') }}
            </span>
            <a href="{{ route('pakets.edit', $paket) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            <a href="{{ route('pakets.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="app-card space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Informasi Paket</p>
                        <h2 class="text-base font-semibold text-gray-900">Detail & status</h2>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $paket->aktif ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        {{ strtoupper($paket->aktif ? 'Aktif' : 'Nonaktif') }}
                    </span>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Paket</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paket->nama_paket }}</dd>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">Harga</dt>
                        <dd class="text-xl font-bold text-green-900">Rp {{ number_format((float)$paket->harga, 0, ',', '.') }}</dd>
                    </div>
                </dl>
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $paket->deskripsi ?: 'Tidak ada deskripsi' }}</dd>
                </div>
            </div>
        </div>

        <div>
            <div class="app-card space-y-4">
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Statistik</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-users text-blue-500"></i>Total Pelanggan</span>
                        <span class="text-lg font-bold text-gray-900">{{ $totalCustomers }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-user-check text-green-500"></i>Pelanggan Aktif</span>
                        <span class="text-lg font-bold text-gray-900">{{ $activeCustomers }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-money-bill-wave text-emerald-500"></i>Potensi Pendapatan</span>
                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pelanggans->count() > 0)
    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-orange-500 font-semibold">Pelanggan Paket Ini</p>
                <h2 class="text-base font-semibold text-gray-900">{{ $pelanggans->total() }} pelanggan terdaftar</h2>
            </div>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-orange-500 to-orange-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-user mr-2"></i>Nama
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-wifi mr-2"></i>PPPoE
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-user-tie mr-2"></i>Penagih
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($pelanggans as $pelanggan)
                    <tr class="hover:bg-orange-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white font-bold text-sm flex items-center justify-center shadow">
                                    {{ substr($pelanggan->nama, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $pelanggan->nama }}</p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-phone"></i>{{ $pelanggan->no_hp }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-xs font-mono text-gray-800 bg-gray-100 px-3 py-2 rounded-xl">{{ $pelanggan->pppoe }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-700 inline-flex items-center px-3 py-2 rounded-xl border {{ $pelanggan->penagih ? 'border-blue-100 bg-blue-50 text-blue-800' : 'border-gray-100 bg-gray-50 text-gray-500' }}">
                                @if($pelanggan->penagih)
                                    <i class="fas fa-user-tie mr-2"></i>{{ $pelanggan->penagih->nama }}
                                @else
                                    <i class="fas fa-user-slash mr-2"></i>Belum ada penagih
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' :
                                   ($pelanggan->status === 'bayar double' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                                {{ strtoupper($pelanggan->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('pelanggans.show', $pelanggan) }}"
                               class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                                <i class="fas fa-eye mr-2"></i>Lihat
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-2">
            @foreach($pelanggans as $pelanggan)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white text-xs font-bold flex items-center justify-center shadow">{{ substr($pelanggan->nama, 0, 1) }}</div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $pelanggan->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $pelanggan->no_hp }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold ml-auto
                        {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' :
                           ($pelanggan->status === 'bayar double' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                        {{ strtoupper($pelanggan->status) }}
                    </span>
                </div>
                <div class="mt-2 text-xs text-gray-600">
                    <p class="font-mono text-gray-800">PPPoE: {{ $pelanggan->pppoe }}</p>
                    <p class="mt-1">
                        @if($pelanggan->penagih)
                            <i class="fas fa-user-tie mr-1 text-blue-500"></i>{{ $pelanggan->penagih->nama }}
                        @else
                            <i class="fas fa-user-slash mr-1 text-gray-400"></i>Belum ada penagih
                        @endif
                    </p>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('pelanggans.show', $pelanggan) }}" class="inline-flex items-center px-3 py-2 text-[11px] bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mr-1.5"></i>Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        @if($pelanggans->hasPages())
        <div class="pt-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs sm:text-sm text-gray-600 gap-3">
                <span>Menampilkan {{ $pelanggans->firstItem() }} - {{ $pelanggans->lastItem() }} dari {{ $pelanggans->total() }} pelanggan</span>
                {{ $pelanggans->appends(request()->query())->onEachSide(1)->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

