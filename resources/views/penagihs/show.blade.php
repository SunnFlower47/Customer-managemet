@extends('layouts.app')

@section('title', 'Detail Penagih - WiFi Billing Management')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-user-tie mr-2 sm:mr-3 text-blue-600"></i>
                            <span class="truncate">{{ $penagih->nama }}</span>
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Detail penagih dan statistik</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a href="{{ route('penagihs.edit', $penagih) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="{{ route('penagihs.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Penagih Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-user mr-1 text-gray-400"></i>Nama Lengkap
                            </label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penagih->nama }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-1 text-gray-400"></i>Email
                            </label>
                            <p class="text-gray-900 break-all">{{ $penagih->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone mr-1 text-gray-400"></i>No. HP
                            </label>
                            <p class="text-gray-900">{{ $penagih->no_hp ?: '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-info-circle mr-1 text-gray-400"></i>Status
                            </label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $penagih->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $penagih->aktif ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ $penagih->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>Alamat
                            </label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900">{{ $penagih->alamat ?: 'Tidak ada alamat' }}</p>
                            </div>
                        </div>

                        @if($penagih->user)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-user-cog mr-1 text-gray-400"></i>Akun User
                            </label>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">{{ $penagih->user->name }}</p>
                                        <p class="text-sm text-blue-700">{{ $penagih->user->email }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($penagih->user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-chart-bar mr-2 text-gray-400"></i>Statistik Penagih
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Pelanggan</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $penagih->pelanggans->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-check text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Pelanggan Aktif</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $penagih->pelanggans->where('status', 'aktif')->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-receipt text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Total Tagihan</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $penagih->pembayarans->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-purple-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Tagihan Lunas</p>
                                    <p class="text-2xl font-bold text-purple-900">{{ $penagih->pembayarans->where('status', 'lunas')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelanggan List -->
        @if($penagih->pelanggans->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-users mr-2 text-gray-400"></i>Pelanggan yang Ditangani
                </h3>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-2 text-gray-400"></i>Nama
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-wifi mr-2 text-gray-400"></i>PPPoE
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-box mr-2 text-gray-400"></i>Paket
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cog mr-2 text-gray-400"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($penagih->pelanggans as $pelanggan)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <span class="text-gray-600 font-semibold text-xs">{{ substr($pelanggan->nama, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $pelanggan->nama }}</div>
                                        <div class="text-sm text-gray-500">{{ $pelanggan->no_hp }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono">{{ $pelanggan->pppoe }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $pelanggan->paket->nama_paket }}</div>
                                <div class="text-sm text-gray-500">Rp {{ number_format($pelanggan->paket->harga, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800' :
                                       ($pelanggan->status === 'suspend' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($pelanggan->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('pelanggans.show', $pelanggan) }}" class="text-blue-600 hover:text-blue-900 transition duration-150">
                                    <i class="fas fa-eye mr-1"></i>Lihat
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="lg:hidden">
                @foreach($penagih->pelanggans as $pelanggan)
                <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-sm">{{ substr($pelanggan->nama, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $pelanggan->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $pelanggan->no_hp }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                                <div>
                                    <span class="font-medium">PPPoE:</span><br>
                                    <span class="font-mono text-blue-600">{{ $pelanggan->pppoe }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Status:</span><br>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800' :
                                           ($pelanggan->status === 'suspend' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($pelanggan->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-xs text-gray-600">
                                <span class="font-medium">Paket:</span>
                                <span class="text-gray-900">{{ $pelanggan->paket->nama_paket }}</span>
                                <span class="text-gray-500 ml-2">Rp {{ number_format($pelanggan->paket->harga, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="ml-4">
                            <a href="{{ route('pelanggans.show', $pelanggan) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Payments -->
        @if($penagih->pembayarans->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-receipt mr-2 text-gray-400"></i>Tagihan Terbaru
                </h3>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-2 text-gray-400"></i>Pelanggan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>Jumlah
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cog mr-2 text-gray-400"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($penagih->pembayarans->take(10) as $pembayaran)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $pembayaran->pelanggan->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $pembayaran->pelanggan->pppoe }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('pembayarans.show', $pembayaran) }}" class="text-blue-600 hover:text-blue-900 transition duration-150">
                                    <i class="fas fa-eye mr-1"></i>Lihat
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="lg:hidden">
                @foreach($penagih->pembayarans->take(10) as $pembayaran)
                <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-sm">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</div>
                                    <div class="text-sm text-gray-500 font-mono">{{ $pembayaran->pelanggan->pppoe }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                                <div>
                                    <span class="font-medium">Periode:</span><br>
                                    <span class="text-gray-900">
                                        {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Jumlah:</span><br>
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                </span>
                            </div>
                        </div>

                        <div class="ml-4">
                            <a href="{{ route('pembayarans.show', $pembayaran) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
</div>

@endsection
