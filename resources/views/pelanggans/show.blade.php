@extends('layouts.app')

@section('title', 'Detail Pelanggan - WiFi Billing Management')

@push('styles')
@if($pelanggan->hasLocation())
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #pelanggan-location-map {
        height: 250px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
    }
    .leaflet-container {
        z-index: 1;
    }
</style>
@endif
@endpush

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl sm:text-2xl">{{ substr($pelanggan->nama, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900 text-lg sm:text-2xl">Detail Pelanggan</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Informasi lengkap {{ $pelanggan->nama }}</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('pelanggans.edit', $pelanggan) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            <a href="{{ route('pelanggans.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-400 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Informasi Pelanggan -->
        <div class="lg:col-span-2">
        <div class="app-card space-y-5 sm:space-y-6">
            <div>
                <h3 class="text-sm sm:text-base font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-user text-blue-500"></i>
                    Informasi Pelanggan
                </h3>
                <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Data lengkap pelanggan WiFi</p>
            </div>
            <div>
                <dl class="grid grid-cols-1 gap-3 sm:gap-4 sm:grid-cols-2">
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-user mr-2 text-blue-600"></i>Nama Lengkap
                            </dt>
                            <dd class="text-sm sm:text-base font-semibold text-gray-900">{{ $pelanggan->nama }}</dd>
                        </div>
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-wifi mr-2 text-blue-600"></i>PPPoE
                            </dt>
                            <dd class="text-sm sm:text-base font-semibold text-gray-900 font-mono">{{ $pelanggan->pppoe }}</dd>
                        </div>
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-phone mr-2 text-blue-600"></i>No. HP
                            </dt>
                            <dd class="text-sm sm:text-base font-semibold text-gray-900">{{ $pelanggan->no_hp }}</dd>
                        </div>
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Status
                            </dt>
                            <dd class="mt-0.5 sm:mt-1">
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold
                                    {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' :
                                       ($pelanggan->status === 'bayar double' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                                    <i class="fas fa-circle mr-1 text-xs"></i>{{ ucfirst($pelanggan->status) }}
                                </span>
                            </dd>
                        </div>
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Tanggal Mulai
                            </dt>
                            <dd class="text-sm sm:text-base font-semibold text-gray-900">{{ $pelanggan->tanggal_mulai ? $pelanggan->tanggal_mulai->format('d M Y') : '-' }}</dd>
                        </div>
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-calendar-check mr-2 text-blue-600"></i>Tanggal Pembayaran
                            </dt>
                            <dd class="text-sm sm:text-base font-semibold text-gray-900">Tanggal {{ $pelanggan->tanggal_pembayaran }}</dd>
                        </div>
                    <div class="sm:col-span-2 bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <dt class="text-[12px] sm:text-sm font-bold text-gray-700 flex items-center mb-1.5 sm:mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Alamat
                            </dt>
                            <dd class="text-sm sm:text-base font-semibold text-gray-900 leading-relaxed">{{ $pelanggan->alamat }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Paket, Penagih & MikroTik -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                <div class="app-card space-y-3.5 sm:space-y-4">
                    <div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-box text-emerald-500"></i>Paket Internet
                        </h3>
                        <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Paket yang sedang aktif</p>
                    </div>
                    <div class="space-y-4">
                        <dl class="space-y-3">
                            <div class="bg-emerald-50 border border-emerald-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                                <dt class="text-[12px] sm:text-sm font-bold text-green-700 mb-1.5 sm:mb-2">Nama Paket</dt>
                                <dd class="text-base sm:text-lg font-bold text-green-900">{{ $pelanggan->paket->nama_paket }}</dd>
                            </div>
                            <div class="bg-emerald-50 border border-emerald-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                                <dt class="text-[12px] sm:text-sm font-bold text-green-700 mb-1.5 sm:mb-2">Harga</dt>
                                <dd class="text-base sm:text-lg font-bold text-green-900">Rp {{ number_format((float)$pelanggan->paket->harga, 0, ',', '.') }}</dd>
                            </div>
                            @if($pelanggan->paket->deskripsi)
                            <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                                <dt class="text-[12px] sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">Deskripsi</dt>
                                <dd class="text-sm sm:text-base font-semibold text-gray-900">{{ $pelanggan->paket->deskripsi }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="app-card space-y-3.5 sm:space-y-4">
                    <div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-user-tie text-purple-500"></i>Penagih
                        </h3>
                        <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Penagih yang bertanggung jawab</p>
                    </div>
                    <div class="space-y-4">
                        <dl class="space-y-3">
                            <div class="bg-blue-50 border border-blue-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                                <dt class="text-[12px] sm:text-sm font-bold text-blue-700 mb-1.5 sm:mb-2">Nama Penagih</dt>
                                <dd class="text-sm sm:text-base font-semibold text-blue-900">
                                    @if($pelanggan->penagih)
                                        {{ $pelanggan->penagih->nama }}
                                    @else
                                        <span class="text-gray-500 italic">Belum ada penagih</span>
                                    @endif
                                </dd>
                            </div>
                            @if($pelanggan->penagih)
                            <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                                <dt class="text-[12px] sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">Email</dt>
                                <dd class="text-sm sm:text-base font-semibold text-gray-900">{{ $pelanggan->penagih->email }}</dd>
                            </div>
                            @if($pelanggan->penagih->no_hp)
                            <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                                <dt class="text-[12px] sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">No. HP</dt>
                                <dd class="text-sm sm:text-base font-semibold text-gray-900">{{ $pelanggan->penagih->no_hp }}</dd>
                            </div>
                            @endif
                            @endif
                        </dl>
                    </div>
                </div>

                @if(isset($mikrotikInfo))
                <div class="app-card space-y-3.5 sm:space-y-4 md:col-span-2">
                    <div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-server text-indigo-500"></i>Status MikroTik
                        </h3>
                        <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Status PPPoE di router</p>
                    </div>
                    <div class="space-y-4">
                        @if($mikrotikInfo['exists'] ?? false)
                        <div class="bg-green-50 border border-green-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <div class="flex items-center justify-between mb-2">
                                <dt class="text-[12px] sm:text-sm font-bold text-green-700">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>Ada di MikroTik
                                    </span>
                                </dd>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <dt class="text-[11px] sm:text-xs font-semibold text-gray-600 mb-1">Router</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $mikrotikInfo['router']->nama ?? 'N/A' }}</dd>
                                </div>
                                @if($mikrotikInfo['ip'] ?? null)
                                <div>
                                    <dt class="text-[11px] sm:text-xs font-semibold text-gray-600 mb-1">IP Address</dt>
                                    <dd class="text-sm font-mono font-semibold text-gray-900">{{ $mikrotikInfo['ip'] }}</dd>
                                </div>
                                @endif
                                @if($mikrotikInfo['profile'] ?? null)
                                <div>
                                    <dt class="text-[11px] sm:text-xs font-semibold text-gray-600 mb-1">Profile</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $mikrotikInfo['profile'] }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-[11px] sm:text-xs font-semibold text-gray-600 mb-1">Status Router</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold
                                            {{ ($mikrotikInfo['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ($mikrotikInfo['status'] ?? 'active') === 'active' ? 'Active' : 'Disabled' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                            @if($pelanggan->mikrotik_last_checked)
                            <p class="text-[10px] text-gray-500 mt-3">Terakhir dicek: {{ $pelanggan->mikrotik_last_checked->diffForHumans() }}</p>
                            @endif
                        </div>
                        @else
                        <div class="bg-red-50 border border-red-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-[12px] sm:text-sm font-bold text-red-700">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle mr-1 text-xs"></i>Tidak ada di MikroTik
                                    </span>
                                </dd>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">PPPoE "{{ $pelanggan->pppoe }}" tidak ditemukan di router yang dikonfigurasi.</p>
                            @if($pelanggan->mikrotik_last_checked)
                            <p class="text-[10px] text-gray-500 mt-2">Terakhir dicek: {{ $pelanggan->mikrotik_last_checked->diffForHumans() }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Lokasi (jika ada koordinat) -->
            @if($pelanggan->hasLocation())
            <div class="app-card space-y-3.5 sm:space-y-4 mt-6">
                <div>
                    <h3 class="text-sm sm:text-base font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-500"></i>Lokasi
                    </h3>
                    <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Koordinat lokasi pelanggan</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 border border-gray-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                        <dt class="text-[12px] sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">Koordinat</dt>
                        <dd class="text-xs sm:text-sm font-semibold text-gray-900">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                                <span><i class="fas fa-latitude mr-1"></i>{{ number_format($pelanggan->latitude, 8) }}</span>
                                <span><i class="fas fa-longitude mr-1"></i>{{ number_format($pelanggan->longitude, 8) }}</span>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $pelanggan->latitude }},{{ $pelanggan->longitude }}"
                                   target="_blank"
                                   class="mt-2 sm:mt-0 ml-auto inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                                </a>
                            </div>
                        </dd>
                    </div>
                    @if($pelanggan->odp)
                    <div class="bg-purple-50 border border-purple-100 px-3.5 py-3 rounded-xl sm:px-4 sm:py-4">
                        <dt class="text-[12px] sm:text-sm font-bold text-purple-700 mb-1.5 sm:mb-2">ODP Terkait</dt>
                        <dd class="text-sm sm:text-base font-semibold text-purple-900">
                            <a href="{{ route('odps.show', $pelanggan->odp) }}" class="hover:underline">
                                {{ $pelanggan->odp->kode_odp }} - {{ $pelanggan->odp->nama }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div id="pelanggan-location-map"></div>
                </div>
            </div>
            @endif
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="lg:col-span-1">
            <div class="app-card space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-history text-orange-500"></i>Riwayat Pembayaran
                        </h3>
                        <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Catatan pembayaran pelanggan</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-600 border border-orange-100">
                        {{ $pembayarans->total() }} total
                    </span>
                </div>

                @if($pembayarans->count() > 0)
                <div class="hidden lg:block space-y-3.5">
                    @foreach($pembayarans as $pembayaran)
                    <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-100 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-3.5">
                                <div class="flex-shrink-0">
                                    <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                                        <span class="text-white font-bold text-base">
                                            {{ $pembayaran->bulan_tagihan }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1.5 gap-3">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }} {{ $pembayaran->tahun_tagihan }}
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-bold {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                            <i class="fas fa-circle mr-1 text-[10px]"></i>{{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                        </span>
                                    </div>
                                    <p class="text-base font-bold text-gray-900 mb-1.5">
                                        Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}
                                    </p>
                                    @if($pembayaran->tanggal_bayar)
                                    <p class="text-xs text-green-600 font-medium flex items-center gap-1">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Dibayar: {{ $pembayaran->tanggal_bayar->format('d M Y H:i') }}
                                    </p>
                                    @else
                                    <p class="text-xs text-gray-500 font-medium flex items-center gap-1">
                                        <i class="fas fa-clock text-xs"></i>
                                        Belum dibayar
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Mobile Cards -->
                    <div class="lg:hidden space-y-2">
                        @foreach($pembayarans as $pembayaran)
                        <div class="mobile-card bg-white border border-gray-200 rounded-2xl p-3 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-2">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                                        <span class="text-white font-bold text-xs">
                                            {{ $pembayaran->bulan_tagihan }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-2 flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-gray-900 truncate">
                                            {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }} {{ $pembayaran->tahun_tagihan }}
                                        </h4>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-lg text-[11px] font-bold
                                            {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                            <i class="fas fa-circle mr-1 text-[9px]"></i>
                                            {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-2.5 mb-2">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-900">
                                        Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            @if($pembayaran->tanggal_bayar)
                            <div class="flex items-center justify-center bg-green-50 rounded-xl p-1.5">
                                <i class="fas fa-check-circle mr-1 text-green-600 text-[11px]"></i>
                                <span class="text-[10px] text-green-700 font-medium">
                                    Dibayar: {{ $pembayaran->tanggal_bayar->format('d M Y H:i') }}
                                </span>
                            </div>
                            @else
                            <div class="flex items-center justify-center bg-gray-100 rounded-xl p-1.5">
                                <i class="fas fa-clock mr-1 text-gray-500 text-[11px]"></i>
                                <span class="text-[10px] text-gray-600 font-medium">
                                    Belum dibayar
                                </span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination for pembayarans -->
                    @if($pembayarans->total() > $pembayarans->perPage())
                    <div class="mt-5 border-t border-gray-200 pt-4 space-y-3">
                        <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                            Menampilkan
                            <span class="font-semibold text-gray-900">{{ $pembayarans->firstItem() }}</span>
                            –
                            <span class="font-semibold text-gray-900">{{ $pembayarans->lastItem() }}</span>
                            dari
                            <span class="font-semibold text-gray-900">{{ $pembayarans->total() }}</span>
                            riwayat pembayaran
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            <div class="w-full sm:w-auto">
                                {{ $pembayarans->appends(request()->query())->onEachSide(1)->links('vendor.pagination.tailwind') }}
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada riwayat pembayaran</h3>
                        <p class="text-gray-500">Pembayaran akan muncul setelah tagihan dibuat.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@if($pelanggan->hasLocation())
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let currentTileLayer = null;
    let satelliteMode = false;

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 19
    });

    function initPelangganMap() {
        const lat = {{ $pelanggan->latitude }};
        const lng = {{ $pelanggan->longitude }};

        const map = L.map('pelanggan-location-map').setView([lat, lng], 15);

        // Add default tile layer
        currentTileLayer = osmLayer;
        currentTileLayer.addTo(map);

        // Add satellite toggle checkbox
        const satelliteControl = L.control({ position: 'topright' });
        satelliteControl.onAdd = function() {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.style.background = 'transparent';
            div.style.border = 'none';
            div.style.boxShadow = 'none';
            div.innerHTML = '<label class="flex items-center gap-2 bg-white px-3 py-2 rounded shadow text-xs font-semibold cursor-pointer hover:bg-gray-50 transition" style="margin: 0; user-select: none;"><input type="checkbox" id="toggle-satellite-pelanggan" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" style="margin: 0; cursor: pointer;"><span style="cursor: pointer;">🛰️ Satelit</span></label>';
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        satelliteControl.addTo(map);

        const checkbox = document.getElementById('toggle-satellite-pelanggan');
        const label = checkbox.parentElement;
        const span = label.querySelector('span');
        
        checkbox.addEventListener('change', function(e) {
            e.stopPropagation();
            satelliteMode = checkbox.checked;
            map.removeLayer(currentTileLayer);
            currentTileLayer = satelliteMode ? satelliteLayer : osmLayer;
            currentTileLayer.addTo(map);
            span.textContent = satelliteMode ? '🗺️ Peta' : '🛰️ Satelit';
        });

        const pelangganIcon = L.divIcon({
            className: 'pelanggan-marker',
            html: '<div style="background: #10b981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><i class="fas fa-home" style="color: white; font-size: 14px;"></i></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        L.marker([lat, lng], { icon: pelangganIcon })
            .addTo(map)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-green-600 mb-1">${@json($pelanggan->nama)}</h3>
                    <p class="text-xs text-gray-600 mb-1">${@json($pelanggan->pppoe)}</p>
                    <p class="text-xs text-gray-500 mb-2">${@json($pelanggan->alamat ?: 'Tidak ada alamat')}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" target="_blank" class="text-blue-600 hover:underline text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                    </a>
                </div>
            `);

        @if($pelanggan->odp)
        // Add ODP marker if exists
        const odpIcon = L.divIcon({
            className: 'odp-marker',
            html: '<div style="background: #9333ea; width: 25px; height: 25px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
            iconSize: [25, 25],
            iconAnchor: [12, 12]
        });

        L.marker([{{ $pelanggan->odp->latitude }}, {{ $pelanggan->odp->longitude }}], { icon: odpIcon })
            .addTo(map)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-purple-600 mb-1">${@json($pelanggan->odp->kode_odp)}</h3>
                    <p class="text-xs text-gray-600">${@json($pelanggan->odp->nama)}</p>
                    <a href="{{ route('odps.show', $pelanggan->odp) }}" class="text-blue-600 hover:underline text-xs mt-2 inline-block">
                        <i class="fas fa-eye mr-1"></i>Detail ODP
                    </a>
                </div>
            `);

        // Fit bounds to show both markers
        const group = new L.featureGroup([
            L.marker([lat, lng]),
            L.marker([{{ $pelanggan->odp->latitude }}, {{ $pelanggan->odp->longitude }}])
        ]);
        map.fitBounds(group.getBounds().pad(0.1));
        @endif
    }

    document.addEventListener('DOMContentLoaded', initPelangganMap);
</script>
@endif
@endpush
