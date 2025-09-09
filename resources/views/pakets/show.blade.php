@extends('layouts.app')

@section('title', 'Detail Paket - WiFi Billing Management')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-box mr-3 text-blue-600"></i>{{ $paket->nama_paket }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Detail paket internet</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('pakets.edit', $paket) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="{{ route('pakets.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Paket Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-tag mr-1 text-gray-400"></i>Nama Paket
                            </label>
                            <p class="text-lg font-semibold text-gray-900">{{ $paket->nama_paket }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-dollar-sign mr-1 text-gray-400"></i>Harga
                            </label>
                            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format((float)$paket->harga, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-info-circle mr-1 text-gray-400"></i>Status
                            </label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $paket->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $paket->aktif ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ $paket->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-align-left mr-1 text-gray-400"></i>Deskripsi
                            </label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900">{{ $paket->deskripsi ?: 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-chart-bar mr-2 text-gray-400"></i>Statistik Paket
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Pelanggan</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $paket->pelanggans->count() }}</p>
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
                                    <p class="text-2xl font-bold text-green-900">{{ $paket->pelanggans->where('status', 'aktif')->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Pendapatan Bulanan</p>
                                    <p class="text-2xl font-bold text-yellow-900">Rp {{ number_format($paket->pelanggans->where('status', 'aktif')->count() * $paket->harga, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelanggan List -->
        @if($paket->pelanggans->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-users mr-2 text-gray-400"></i>Pelanggan yang Menggunakan Paket Ini
                </h3>
            </div>
            <div class="overflow-x-auto">
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
                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>Penagih
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
                        @foreach($paket->pelanggans as $pelanggan)
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
                                <div class="text-sm text-gray-900">
                                    @if($pelanggan->penagih)
                                        {{ $pelanggan->penagih->nama }}
                                    @else
                                        <span class="text-gray-400 italic">Belum ada penagih</span>
                                    @endif
                                </div>
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
        </div>
        @endif
</div>

@endsection
