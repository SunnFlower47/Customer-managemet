@extends('layouts.app')

@section('title', 'Detail Penagih - WiFi Billing Management')

@section('content')
@php
    $totalPelanggan = $penagih->pelanggans->count();
    $activePelanggan = $penagih->pelanggans->where('status', 'aktif')->count();
    $totalTagihan = $penagih->pembayarans->count();
    $tagihanLunas = $penagih->pembayarans->where('status', 'lunas')->count();
@endphp

<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <span class="font-bold">{{ substr($penagih->nama, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $penagih->nama }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Detail penagih dan statistik</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold {{ $penagih->aktif ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }} rounded-xl">
                {{ $penagih->aktif ? 'Aktif' : 'Nonaktif' }}
            </span>
            <a href="{{ route('penagihs.edit', $penagih) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            <a href="{{ route('penagihs.index') }}"
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
                        <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Informasi Penagih</p>
                        <h2 class="text-base font-semibold text-gray-900">Detail lengkap</h2>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Lengkap</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $penagih->nama }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</dt>
                        <dd class="text-sm font-semibold text-gray-900 truncate" title="{{ $penagih->email }}">{{ $penagih->email }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">No. HP</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $penagih->no_hp ?: '-' }}</dd>
                    </div>
                    @if($penagih->user)
                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">Akun User</dt>
                        <dd class="text-sm font-semibold text-blue-900">{{ $penagih->user->name }}</dd>
                    </div>
                    @endif
                </dl>
                @if($penagih->alamat)
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Alamat</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $penagih->alamat }}</dd>
                </div>
                @endif
            </div>
        </div>

        <div>
            <div class="app-card space-y-4">
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Statistik</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-users text-blue-500"></i>Total Pelanggan</span>
                        <span class="text-lg font-bold text-gray-900">{{ $totalPelanggan }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-user-check text-green-500"></i>Pelanggan Aktif</span>
                        <span class="text-lg font-bold text-gray-900">{{ $activePelanggan }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-receipt text-yellow-500"></i>Total Tagihan</span>
                        <span class="text-lg font-bold text-gray-900">{{ $totalTagihan }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-money-bill-wave text-emerald-500"></i>Tagihan Lunas</span>
                        <span class="text-lg font-bold text-gray-900">{{ $tagihanLunas }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($penagih->pelanggans->count() > 0)
    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-orange-500 font-semibold">Pelanggan yang Ditangani</p>
                <h2 class="text-base font-semibold text-gray-900">{{ $totalPelanggan }} pelanggan</h2>
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
                            <i class="fas fa-box mr-2"></i>Paket
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
                    @foreach($penagih->pelanggans as $pelanggan)
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
                            <div class="text-xs text-gray-700">
                                <p class="font-semibold">{{ $pelanggan->paket->nama_paket }}</p>
                                <p class="text-gray-500">Rp {{ number_format($pelanggan->paket->harga, 0, ',', '.') }}</p>
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
            @foreach($penagih->pelanggans as $pelanggan)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white text-xs font-bold flex items-center justify-center shadow">{{ substr($pelanggan->nama, 0, 1) }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $pelanggan->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $pelanggan->no_hp }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold ml-auto
                        {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' :
                           ($pelanggan->status === 'bayar double' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                        {{ strtoupper($pelanggan->status) }}
                    </span>
                </div>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">PPPoE:</span>
                        <span class="font-mono text-blue-600">{{ $pelanggan->pppoe }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Paket:</span>
                        <span class="text-gray-900">{{ $pelanggan->paket->nama_paket }}</span>
                        <span class="text-gray-500">Rp {{ number_format($pelanggan->paket->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="mt-2 flex justify-end">
                    <a href="{{ route('pelanggans.show', $pelanggan) }}"
                       class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mr-1"></i>Lihat
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($penagih->pembayarans->count() > 0)
    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-yellow-500 font-semibold">Tagihan Terbaru</p>
                <h2 class="text-base font-semibold text-gray-900">{{ $totalTagihan }} tagihan</h2>
            </div>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-yellow-500 to-yellow-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-user mr-2"></i>Pelanggan
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-calendar mr-2"></i>Periode
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wide">
                            <i class="fas fa-dollar-sign mr-2"></i>Jumlah
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
                    @foreach($penagih->pembayarans->take(10) as $pembayaran)
                    <tr class="hover:bg-yellow-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $pembayaran->pelanggan->pppoe }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700">
                                {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('pembayarans.show', $pembayaran) }}"
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
            @foreach($penagih->pembayarans->take(10) as $pembayaran)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white text-xs font-bold flex items-center justify-center shadow">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $pembayaran->pelanggan->pppoe }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold ml-auto {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                </div>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Periode:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Jumlah:</span>
                        <span class="text-gray-900 font-semibold">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="mt-2 flex justify-end">
                    <a href="{{ route('pembayarans.show', $pembayaran) }}"
                       class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mr-1"></i>Lihat
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
