@extends('layouts.app')

@section('title', 'Detail Pelanggan - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Detail Pelanggan</h1>
            <p class="mt-2 text-sm text-gray-700">Informasi lengkap pelanggan {{ $pelanggan->nama }}.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-3">
            <a href="{{ route('pelanggans.edit', $pelanggan) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('pelanggans.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                Kembali
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informasi Pelanggan -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informasi Pelanggan</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PPPoE</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $pelanggan->pppoe }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">No. HP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->no_hp }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800' :
                                       ($pelanggan->status === 'suspend' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($pelanggan->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Mulai</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->tanggal_mulai ? $pelanggan->tanggal_mulai->format('d M Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pembayaran</dt>
                            <dd class="mt-1 text-sm text-gray-900">Tanggal {{ $pelanggan->tanggal_pembayaran }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->alamat }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Paket & Penagih -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Paket Internet</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Paket</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->paket->nama_paket }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Harga</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">Rp {{ number_format((float)$pelanggan->paket->harga, 0, ',', '.') }}</dd>
                            </div>
                            @if($pelanggan->paket->deskripsi)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->paket->deskripsi }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Penagih</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Penagih</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($pelanggan->penagih)
                                        {{ $pelanggan->penagih->nama }}
                                    @else
                                        <span class="text-gray-400 italic">Belum ada penagih</span>
                                    @endif
                                </dd>
                            </div>
                            @if($pelanggan->penagih)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->penagih->email }}</dd>
                            </div>
                            @if($pelanggan->penagih->no_hp)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. HP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $pelanggan->penagih->no_hp }}</dd>
                            </div>
                            @endif
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Riwayat Pembayaran</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            {{ $pembayarans->total() }} total
                        </span>
                    </div>

                    @if($pembayarans->count() > 0)
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200">
                            @foreach($pembayarans as $pembayaran)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-100 to-primary-200 flex items-center justify-center shadow-sm">
                                            <span class="text-sm font-bold text-primary-700">
                                                {{ $pembayaran->bulan_tagihan }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }} {{ $pembayaran->tahun_tagihan }}
                                            </p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}
                                        </p>
                                        @if($pembayaran->tanggal_bayar)
                                        <p class="text-xs text-green-600 font-medium">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Dibayar: {{ $pembayaran->tanggal_bayar->format('d M Y H:i') }}
                                        </p>
                                        @else
                                        <p class="text-xs text-gray-400">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Belum dibayar
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>

                        <!-- Pagination for pembayarans -->
                        @if($pembayarans->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Menampilkan
                                    <span class="font-medium">{{ $pembayarans->firstItem() }}</span>
                                    sampai
                                    <span class="font-medium">{{ $pembayarans->lastItem() }}</span>
                                    dari
                                    <span class="font-medium">{{ $pembayarans->total() }}</span>
                                    pembayaran
                                </div>
                                <div class="flex space-x-2">
                                    {{ $pembayarans->links('vendor.pagination.tailwind') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat pembayaran</h3>
                        <p class="mt-1 text-sm text-gray-500">Pembayaran akan muncul setelah tagihan dibuat.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
