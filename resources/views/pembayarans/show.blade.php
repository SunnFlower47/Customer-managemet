@extends('layouts.app')

@section('title', 'Detail Pembayaran - WiFi Billing Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-receipt mr-3 text-blue-600"></i>Detail Pembayaran
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap tagihan dan pembayaran</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('pembayarans.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="space-y-6">
        <!-- Customer Info -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-user mr-3 text-blue-600"></i>Informasi Pelanggan
            </h3>
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-xl">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</span>
                    </div>
                </div>
                <div class="ml-6">
                    <h4 class="text-xl font-semibold text-gray-900">{{ $pembayaran->pelanggan->nama }}</h4>
                    <p class="text-sm text-gray-500 font-mono">{{ $pembayaran->pelanggan->pppoe }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                    <p class="text-gray-900">{{ $pembayaran->pelanggan->no_hp ?: '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <p class="text-gray-900">{{ $pembayaran->pelanggan->alamat ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-receipt mr-3 text-green-600"></i>Informasi Pembayaran
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pembayaran</label>
                    <p class="text-sm font-mono text-gray-900 bg-gray-50 p-2 rounded">{{ $pembayaran->kode_pembayaran }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Tagihan</label>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }} {{ $pembayaran->tahun_tagihan }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tagihan</label>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Paket</label>
                    <p class="text-gray-900">
                        @if($pembayaran->nama_paket)
                            {{ $pembayaran->nama_paket }}<br>
                            <span class="text-sm text-gray-600">Rp {{ number_format((float)$pembayaran->harga_paket, 0, ',', '.') }}</span>
                        @else
                            {{ $pembayaran->pelanggan->paket->nama_paket }}<br>
                            <span class="text-sm text-gray-600">Rp {{ number_format((float)$pembayaran->pelanggan->paket->harga, 0, ',', '.') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Status & Actions -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-purple-600"></i>Status & Aksi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="fas {{ $pembayaran->status === 'lunas' ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-2"></i>
                        {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Bayar</label>
                    <p class="text-gray-900">
                        {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d M Y H:i') : '-' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penagih</label>
                    <p class="text-gray-900">
                        @if($pembayaran->nama_penagih)
                            {{ $pembayaran->nama_penagih }}
                        @elseif($pembayaran->penagih)
                            {{ $pembayaran->penagih->nama }}
                        @else
                            <span class="text-gray-400 italic">Belum ada penagih</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($pembayaran->status === 'belum_bayar')
            <div class="mt-6 pt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('pembayarans.mark-paid', $pembayaran) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200"
                            onclick="return confirm('Tandai pembayaran ini sebagai lunas?')">
                        <i class="fas fa-check mr-2"></i>Tandai Sebagai Lunas
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Keterangan Section -->
        @if($pembayaran->keterangan)
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-comment mr-3 text-gray-600"></i>Keterangan
            </h3>
            <p class="text-gray-900">{{ $pembayaran->keterangan }}</p>
        </div>
        @endif

        <!-- Payment History -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-history mr-3 text-gray-400"></i>Riwayat Pembayaran Pelanggan
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>Jumlah
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-clock mr-2 text-gray-400"></i>Tanggal Bayar
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($paymentHistory as $history)
                        <tr class="hover:bg-gray-50 transition duration-150 {{ $history->id === $pembayaran->id ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::create(null, $history->bulan_tagihan, 1)->format('M') }} {{ $history->tahun_tagihan }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($history->jumlah, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $history->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $history->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $history->tanggal_bayar ? $history->tanggal_bayar->format('d M Y') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada riwayat pembayaran</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($paymentHistory->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $paymentHistory->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
