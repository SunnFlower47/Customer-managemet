@extends('layouts.app')

@section('title', 'Detail Pembayaran - WiFi Billing Management')

@section('content')
@php
    $dueDate = \Carbon\Carbon::create(
        $pembayaran->tahun_tagihan,
        $pembayaran->bulan_tagihan,
        $pembayaran->pelanggan->tanggal_pembayaran
    );
    $isOverdue = $dueDate->isPast() && $pembayaran->status !== 'lunas';
@endphp

<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Detail Pembayaran</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Periksa informasi tagihan dan status pembayarannya</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('pembayarans.edit', $pembayaran) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            @if($pembayaran->status === 'lunas')
            <a href="{{ route('pembayarans.invoice', $pembayaran) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-print mr-2 text-xs sm:text-sm"></i>Faktur
            </a>
            @endif
            <a href="{{ route('pembayarans.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="app-card space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Informasi Pelanggan</p>
                        <h2 class="text-base font-semibold text-gray-900">Profil & Kontak</h2>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                        Pelanggan
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-lg flex items-center justify-center shadow">
                        {{ substr($pembayaran->pelanggan->nama, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                        <p class="text-xs font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg inline-flex items-center gap-2 mt-1">
                            <i class="fas fa-wifi"></i>{{ $pembayaran->pelanggan->pppoe }}
                        </p>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                            <i class="fas fa-phone text-blue-500"></i>No. HP
                        </dt>
                        <dd class="text-sm font-semibold text-gray-900 mt-1">{{ $pembayaran->pelanggan->no_hp ?: '-' }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-500"></i>Alamat
                        </dt>
                        <dd class="text-sm font-semibold text-gray-900 mt-1 leading-relaxed">{{ $pembayaran->pelanggan->alamat ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="app-card space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Detail Pembayaran</p>
                        <h2 class="text-base font-semibold text-gray-900">Tagihan & Paket</h2>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-green-50 text-green-600 border border-green-100">
                        ID {{ $pembayaran->kode_pembayaran }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kode Pembayaran</dt>
                        <dd class="text-sm font-mono text-gray-900">{{ $pembayaran->kode_pembayaran }}</dd>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-yellow-800 uppercase tracking-wide mb-1">Periode</dt>
                        <dd class="text-base font-bold text-yellow-900">
                            {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }}
                            {{ $pembayaran->tahun_tagihan }}
                        </dd>
                    </div>
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-green-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">Jumlah Tagihan</dt>
                        <dd class="text-xl font-bold text-green-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</dd>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">Paket</dt>
                        <dd class="text-sm font-semibold text-blue-900">
                            @if($pembayaran->nama_paket)
                                {{ $pembayaran->nama_paket }}
                                <span class="block text-xs text-blue-600 font-bold">Rp {{ number_format((float)$pembayaran->harga_paket, 0, ',', '.') }}</span>
                            @elseif($pembayaran->pelanggan->paket)
                                {{ $pembayaran->pelanggan->paket->nama_paket }}
                                <span class="block text-xs text-blue-600 font-bold">Rp {{ number_format((float)$pembayaran->pelanggan->paket->harga, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-500 italic">Tidak ada paket</span>
                            @endif
                        </dd>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 flex flex-col gap-1">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Jatuh Tempo</span>
                        <span class="text-sm font-semibold {{ $isOverdue ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $dueDate->format('d M Y') }}
                            @if($isOverdue)
                                <i class="fas fa-exclamation-triangle ml-1 text-red-500" title="Lewat jatuh tempo"></i>
                            @endif
                        </span>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 flex flex-col gap-1">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Bayar</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d M Y H:i') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="app-card space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Status Pembayaran</p>
                        <h2 class="text-base font-semibold text-gray-900">Ringkasan Aksi</h2>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                        {{ $pembayaran->kode_pembayaran }}
                    </span>
                </div>
                <span class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-bold
                    {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                    <i class="fas {{ $pembayaran->status === 'lunas' ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-2"></i>
                    {{ strtoupper($pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar') }}
                </span>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="font-medium flex items-center gap-2">
                            <i class="fas fa-user-tie text-purple-500"></i>Penagih
                        </span>
                        <span class="text-gray-900 font-semibold text-right">
                            @if($pembayaran->nama_penagih)
                                {{ $pembayaran->nama_penagih }}
                            @elseif($pembayaran->penagih)
                                {{ $pembayaran->penagih->nama }}
                            @else
                                <span class="text-gray-400 italic">Belum ditentukan</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="font-medium flex items-center gap-2">
                            <i class="fas fa-history text-purple-500"></i>Diperbarui
                        </span>
                        <span class="text-gray-900 font-semibold text-right">
                            {{ $pembayaran->updated_at->format('d M Y H:i') }}
                        </span>
                    </div>
                </dl>

                <div class="pt-4 border-t border-gray-100">
                    @if($pembayaran->status === 'belum_bayar')
                    <form method="POST" action="{{ route('pembayarans.mark-paid', $pembayaran) }}" class="space-y-3">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg transition"
                                onclick="return confirm('Tandai pembayaran ini sebagai lunas?')">
                            <i class="fas fa-check mr-2"></i>Tandai Lunas
                        </button>
                    </form>
                    @else
                    <a href="{{ route('pembayarans.invoice', $pembayaran) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-print mr-2"></i>Lihat Faktur
                    </a>
                    @endif
                </div>
            </div>

            @if($pembayaran->keterangan)
            <div class="app-card space-y-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 rounded-xl bg-orange-100 text-orange-500 items-center justify-center">
                        <i class="fas fa-comment"></i>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-orange-500 font-semibold">Catatan</p>
                        <h2 class="text-base font-semibold text-gray-900">Keterangan Pembayaran</h2>
                    </div>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ $pembayaran->keterangan }}
                </p>
            </div>
            @endif
        </div>
    </div>

    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Riwayat Pembayaran</p>
                <h2 class="text-base font-semibold text-gray-900">Histori pelanggan ini</h2>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                {{ $paymentHistory->total() }} transaksi
            </span>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wide">
                            <i class="fas fa-calendar mr-2 text-gray-400"></i>Periode
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wide">
                            <i class="fas fa-money-bill mr-2 text-gray-400"></i>Jumlah
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wide">
                            <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wide">
                            <i class="fas fa-clock mr-2 text-gray-400"></i>Tanggal Bayar
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($paymentHistory as $history)
                    <tr class="{{ $history->id === $pembayaran->id ? 'bg-green-50' : 'hover:bg-gray-50' }}">
                        <td class="px-5 py-4 text-sm text-gray-900 font-semibold">
                            {{ \Carbon\Carbon::create(null, $history->bulan_tagihan, 1)->format('M') }} {{ $history->tahun_tagihan }}
                        </td>
                        <td class="px-5 py-4 text-sm font-bold text-gray-900">
                            Rp {{ number_format($history->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $history->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                {{ strtoupper($history->status === 'lunas' ? 'Lunas' : 'Belum') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            {{ $history->tanggal_bayar ? $history->tanggal_bayar->format('d M Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Tidak ada riwayat pembayaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-2">
            @forelse($paymentHistory as $history)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3 {{ $history->id === $pembayaran->id ? 'bg-green-50 border-green-100' : 'bg-white' }}">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::create(null, $history->bulan_tagihan, 1)->format('M') }} {{ $history->tahun_tagihan }}
                    </p>
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold {{ $history->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        {{ strtoupper($history->status === 'lunas' ? 'Lunas' : 'Belum') }}
                    </span>
                </div>
                <p class="text-base font-bold text-gray-900 mt-1">Rp {{ number_format($history->jumlah, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">
                    {{ $history->tanggal_bayar ? $history->tanggal_bayar->format('d M Y') : 'Belum dibayar' }}
                </p>
            </div>
            @empty
            <div class="text-center py-10 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p>Tidak ada riwayat pembayaran</p>
            </div>
            @endforelse
        </div>

        @if($paymentHistory->hasPages())
        <div class="pt-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs sm:text-sm text-gray-600 gap-3">
                <span>Menampilkan {{ $paymentHistory->firstItem() }} - {{ $paymentHistory->lastItem() }} dari {{ $paymentHistory->total() }} histori</span>
                {{ $paymentHistory->appends(request()->query())->onEachSide(1)->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
