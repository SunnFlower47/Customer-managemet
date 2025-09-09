@extends('layouts.app')

@section('title', 'Pembayaran - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Data Pembayaran</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola data pembayaran dan tagihan pelanggan.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <div class="text-sm text-gray-500 bg-blue-50 px-3 py-2 rounded-md mb-3">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="hidden sm:inline">Pembayaran dibuat otomatis berdasarkan tanggal pembayaran pelanggan</span>
                <span class="sm:hidden">Pembayaran otomatis</span>
            </div>
            @if(in_array(auth()->user()?->role ?? 'guest', ['admin', 'operator']))
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pembayarans.export', request()->query()) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                    <span class="hidden sm:inline">Export PDF</span>
                    <span class="sm:hidden">PDF</span>
                </a>
                <a href="{{ route('pembayarans.export.excel', request()->query()) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i>
                    <span class="hidden sm:inline">Export Excel</span>
                    <span class="sm:hidden">Excel</span>
                </a>
                <a href="{{ route('pembayarans.export.csv', request()->query()) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-file-csv mr-2 text-blue-600"></i>
                    <span class="hidden sm:inline">Export CSV</span>
                    <span class="sm:hidden">CSV</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Search -->
    <div class="mt-6 bg-white shadow-lg rounded-xl p-4 sm:p-6 border border-gray-100">
        <form method="GET" action="{{ route('pembayarans.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, PPPoE, HP, alamat, atau kode..."
                       class="flex-1 px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-l-lg sm:rounded-r-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                <button type="submit" class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg sm:rounded-l-none sm:rounded-r-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    <i class="fas fa-search mr-1 sm:mr-0"></i>
                    <span class="sm:hidden">Cari</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Filters -->
    <div class="mt-4 bg-white shadow-lg rounded-xl p-4 sm:p-6 border border-gray-100">
        <form method="GET" action="{{ route('pembayarans.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div>
                <label for="penagih_id" class="block text-sm font-semibold text-gray-700 mb-2">Penagih</label>
                <div class="relative">
                    <input type="text"
                           id="penagih_search"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Cari penagih..."
                           autocomplete="off">
                    <input type="hidden" name="penagih_id" id="penagih_id" value="{{ request('penagih_id') }}">
                    <div id="penagih_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                        <div class="px-4 py-2 text-gray-500 cursor-pointer hover:bg-gray-100" data-value="">
                            Semua Penagih
                        </div>
                        @if(isset($penagihs) && $penagihs->count() > 0)
                            @foreach($penagihs as $penagih)
                                <div class="px-4 py-2 cursor-pointer hover:bg-gray-100" data-value="{{ $penagih->id }}" data-name="{{ $penagih->nama }}">
                                    {{ $penagih->nama }}
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-2 text-gray-500">
                                Tidak ada penagih
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                <select name="bulan" id="bulan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                <select name="tahun" id="tahun" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-semibold">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-barcode mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Kode Pembayaran</span>
                            <span class="sm:hidden">Kode</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user mr-2 text-gray-400"></i>Pelanggan
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Penagih</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Periode</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-money-bill mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Jumlah</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Status</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Jatuh Tempo</span>
                            <span class="sm:hidden">Tempo</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-clock mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Tanggal Bayar</span>
                            <span class="sm:hidden">Bayar</span>
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-2 text-gray-400"></i>
                            <span class="hidden sm:inline">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayarans as $pembayaran)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm font-medium text-blue-600 break-all">
                                <span class="hidden sm:inline">{{ $pembayaran->kode_pembayaran }}</span>
                                <span class="sm:hidden">{{ substr($pembayaran->kode_pembayaran, 0, 6) }}...</span>
                            </div>
                            <div class="text-xs text-gray-500 sm:hidden mt-1">
                                {{ $pembayaran->pelanggan->pppoe }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-xs sm:text-sm">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-2 sm:ml-4 min-w-0 flex-1">
                                    <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</div>
                                    <div class="text-xs sm:text-sm text-gray-500 hidden sm:block">{{ $pembayaran->pelanggan->pppoe }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm text-gray-900 truncate">
                                {{ $pembayaran->historical_collector_name }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm text-gray-900">
                                {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <div class="text-xs sm:text-sm font-medium text-gray-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</div>
                            <div class="text-xs sm:text-sm text-gray-500 hidden sm:block">
                                @if($pembayaran->pelanggan->paket)
                                    {{ $pembayaran->pelanggan->paket->nama_paket }}
                                @else
                                    <span class="text-red-500 italic">Paket tidak ditemukan</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                                $dueDate = \Carbon\Carbon::create($pembayaran->tahun_tagihan, $pembayaran->bulan_tagihan, $pembayaran->pelanggan->tanggal_pembayaran);
                                $isOverdue = $dueDate->isPast() && $pembayaran->status !== 'lunas';
                            @endphp
                            <div class="flex items-center">
                                <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    {{ $dueDate->format('d M Y') }}
                                </span>
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-triangle text-red-500 ml-1" title="Jatuh tempo"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm text-gray-900">
                            <div class="text-xs sm:text-sm">
                                {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d M Y H:i') : '-' }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-center text-sm font-medium">
                            <div class="flex flex-col sm:flex-row justify-center space-y-1 sm:space-y-0 sm:space-x-2">
                                <!-- Tombol Detail -->
                                <a href="{{ route('pembayarans.show', $pembayaran) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition duration-150"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>

                                <!-- Tombol Edit -->
                                @can('edit-pembayaran')
                                <a href="{{ route('pembayarans.edit', array_merge([$pembayaran], request()->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']))) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition duration-150"
                                   title="Edit Pembayaran">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @endcan

                                <!-- Tombol Status -->
                                <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 {{ $pembayaran->status === 'belum_bayar' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }} rounded-md transition duration-150"
                                        data-pembayaran-id="{{ $pembayaran->id }}"
                                        data-current-status="{{ $pembayaran->status }}"
                                        data-pelanggan-nama="{{ $pembayaran->pelanggan->nama }}"
                                        onclick="updateStatus({{ $pembayaran->id }}, '{{ $pembayaran->status }}', '{{ $pembayaran->pelanggan->nama }}')"
                                        title="{{ $pembayaran->status === 'belum_bayar' ? 'Tandai sebagai Lunas' : 'Ubah ke Belum Bayar' }}">
                                    @if($pembayaran->status === 'belum_bayar')
                                        <i class="fas fa-check mr-1"></i>Lunas
                                    @else
                                        <i class="fas fa-undo mr-1"></i>Batal
                                    @endif
                                </button>

                                <!-- Tombol Cetak Faktur (hanya untuk status LUNAS) -->
                                @if($pembayaran->status === 'lunas')
                                <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition duration-150"
                                        data-pembayaran-id="{{ $pembayaran->id }}"
                                        data-kode-pembayaran="{{ $pembayaran->kode_pembayaran }}"
                                        data-pelanggan-nama="{{ $pembayaran->pelanggan->nama }}"
                                        onclick="printInvoice({{ $pembayaran->id }}, '{{ $pembayaran->kode_pembayaran }}', '{{ $pembayaran->pelanggan->nama }}')"
                                        title="Cetak Faktur">
                                    <i class="fas fa-print mr-1"></i>Faktur
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pembayaran</h3>
                                <p class="text-gray-500">Belum ada data pembayaran yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            @forelse($pembayarans as $pembayaran)
            <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold text-sm">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $pembayaran->pelanggan->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $pembayaran->pelanggan->no_hp }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                            <div>
                                <span class="font-medium">Kode:</span><br>
                                <span class="font-mono text-blue-600 break-all">{{ substr($pembayaran->kode_pembayaran, 0, 6) }}...</span>
                            </div>
                            <div>
                                <span class="font-medium">PPPoE:</span><br>
                                <span class="font-mono text-green-600">{{ $pembayaran->pelanggan->pppoe }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Penagih:</span><br>
                                <span>{{ $pembayaran->historical_collector_name }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Periode:</span><br>
                                <span>{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Jumlah:</span><br>
                                <span class="font-semibold">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Jatuh Tempo:</span><br>
                                @php
                                    $dueDate = \Carbon\Carbon::create($pembayaran->tahun_tagihan, $pembayaran->bulan_tagihan, $pembayaran->pelanggan->tanggal_pembayaran);
                                    $isOverdue = $dueDate->isPast() && $pembayaran->status !== 'lunas';
                                @endphp
                                <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    {{ $dueDate->format('d/m/Y') }}
                                    @if($isOverdue)
                                        <i class="fas fa-exclamation-triangle text-red-500 ml-1" title="Jatuh tempo"></i>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Tanggal Bayar:</span><br>
                                <span>{{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($pembayaran->status === 'lunas') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $pembayaran->status === 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}
                            </span>
                            @if($pembayaran->tanggal_bayar)
                            <span class="text-xs text-gray-500">
                                Bayar: {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 ml-4">
                        <a href="{{ route('pembayarans.show', $pembayaran) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        @can('edit-pembayaran')
                        <a href="{{ route('pembayarans.edit', array_merge([$pembayaran], request()->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']))) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        @endcan
                        @if($pembayaran->status === 'lunas')
                        <a href="{{ route('pembayarans.invoice', $pembayaran) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-print mr-1"></i>Cetak
                        </a>
                        @endif
                        <button onclick="updateStatus({{ $pembayaran->id }}, '{{ $pembayaran->status }}', '{{ $pembayaran->pelanggan->nama }}')"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white
                                @if($pembayaran->status === 'lunas') bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                                @else bg-green-600 hover:bg-green-700 focus:ring-green-500 @endif
                                focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <i class="fas {{ $pembayaran->status === 'lunas' ? 'fa-undo' : 'fa-check' }} mr-1"></i>
                            {{ $pembayaran->status === 'lunas' ? 'Batal' : 'Lunas' }}
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-money-bill-wave text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pembayaran</h3>
                <p class="text-gray-500">Belum ada data pembayaran yang ditemukan.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pembayarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Ensure SweetAlert is loaded
if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 not loaded!');
}

// Searchable Penagih Dropdown
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('penagih_search');
    const hiddenInput = document.getElementById('penagih_id');
    const dropdown = document.getElementById('penagih_dropdown');

    if (searchInput && hiddenInput && dropdown) {
        const allOptions = dropdown.querySelectorAll('[data-value]');
        let isDropdownOpen = false;
        let clickTimeout = null;

        // Set initial value if selected
        const selectedValue = hiddenInput.value;
        if (selectedValue) {
            const selectedOption = dropdown.querySelector(`[data-value="${selectedValue}"]`);
            if (selectedOption) {
                searchInput.value = selectedOption.dataset.name || selectedOption.textContent.trim();
            }
        }

        // Show dropdown
        function showDropdown() {
            dropdown.classList.remove('hidden');
            isDropdownOpen = true;
            filterOptions();
        }

        // Hide dropdown
        function hideDropdown() {
            dropdown.classList.add('hidden');
            isDropdownOpen = false;
        }

        // Filter options based on search
        function filterOptions() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            allOptions.forEach(option => {
                const text = option.textContent.toLowerCase().trim();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        // Show/hide dropdown
        searchInput.addEventListener('focus', function() {
            showDropdown();
        });

        searchInput.addEventListener('blur', function() {
            // Delay hiding to allow click on options
            clickTimeout = setTimeout(() => {
                hideDropdown();
            }, 300);
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            showDropdown();
        });

        // Handle option selection
        dropdown.addEventListener('mousedown', function(e) {
            e.preventDefault(); // Prevent input blur
        });

        dropdown.addEventListener('click', function(e) {
            const option = e.target.closest('[data-value]');
            if (option) {
                const value = option.dataset.value;
                const name = option.dataset.name || option.textContent.trim();

                // Clear timeout to prevent hiding
                if (clickTimeout) {
                    clearTimeout(clickTimeout);
                }

                hiddenInput.value = value;
                searchInput.value = name;
                hideDropdown();

                console.log('Penagih selected:', name, 'ID:', value);
            }
        });

        // Handle escape key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDropdown();
            }
        });
    }
});

// Ensure functions are available globally
window.updateStatus = function(pembayaranId, currentStatus, pelangganNama) {
    console.log('updateStatus function called:', pembayaranId, currentStatus, pelangganNama);

    const newStatus = currentStatus === 'belum_bayar' ? 'lunas' : 'belum_bayar';
    const statusText = newStatus === 'lunas' ? 'LUNAS' : 'BELUM BAYAR';
    const statusColor = newStatus === 'lunas' ? '#10B981' : '#F59E0B';

    // Jika mengubah ke LUNAS, tampilkan form dengan keterangan
    if (newStatus === 'lunas') {
        Swal.fire({
            title: 'Konfirmasi Pembayaran Lunas',
            html: `
                <div class="text-left">
                    <p class="mb-4">Apakah Anda yakin ingin menandai pembayaran sebagai <strong>LUNAS</strong>?</p>
                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                        <textarea id="keterangan"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  rows="3"
                                  placeholder="Masukkan keterangan tambahan..."></textarea>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="cetak_faktur" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Cetak Faktur setelah update</span>
                        </label>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: statusColor,
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Tandai Lunas!',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const keterangan = document.getElementById('keterangan').value;
                const cetakFaktur = document.getElementById('cetak_faktur').checked;

                console.log('Sending request to update status with keterangan...');
                // Get current pagination and filter parameters
                const urlParams = new URLSearchParams(window.location.search);
                const paginationParams = {
                    page: urlParams.get('page'),
                    search: urlParams.get('search'),
                    status: urlParams.get('status'),
                    penagih_id: urlParams.get('penagih_id'),
                    bulan: urlParams.get('bulan'),
                    tahun: urlParams.get('tahun')
                };

                return fetch(`/pembayarans/${pembayaranId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        keterangan: keterangan,
                        cetak_faktur: cetakFaktur
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }

                    return response.text().then(text => {
                        console.log('Raw response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON response');
                        }
                    });
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (!data.success) {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                    return data;
                })
                .catch(error => {
                    console.error('Request error:', error);
                    Swal.showValidationMessage(`Request failed: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Show success message
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    // If cetak_faktur was checked, show invoice
                    if (data.cetak_faktur) {
                        window.open(`{{ url('/pembayarans') }}/${pembayaranId}/invoice`, '_blank');
                    }

                    // Reload page to show updated status
                    window.location.reload();
                });
            }
        });
    } else {
        // Untuk status "Belum Bayar" - konfirmasi sederhana
        Swal.fire({
            title: 'Update Status Pembayaran',
            text: `Apakah Anda yakin ingin mengubah status menjadi ${statusText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: statusColor,
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                console.log('Sending request to update status...');
                // Get current pagination and filter parameters
                const urlParams = new URLSearchParams(window.location.search);
                const paginationParams = {
                    page: urlParams.get('page'),
                    search: urlParams.get('search'),
                    status: urlParams.get('status'),
                    penagih_id: urlParams.get('penagih_id'),
                    bulan: urlParams.get('bulan'),
                    tahun: urlParams.get('tahun')
                };

                return fetch(`/pembayarans/${pembayaranId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }

                    return response.text().then(text => {
                        console.log('Raw response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON response');
                        }
                    });
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (!data.success) {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                    return data;
                })
                .catch(error => {
                    console.error('Request error:', error);
                    Swal.showValidationMessage(`Request failed: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Show success message
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    // Reload page to show updated status
                    window.location.reload();
                });
            }
        });
    }
}

// Ensure printInvoice function is available globally
window.printInvoice = function(pembayaranId, kodePembayaran, pelangganNama) {
    console.log('printInvoice function called:', pembayaranId, kodePembayaran, pelangganNama);

    Swal.fire({
        title: 'Cetak Faktur',
        text: `Apakah Anda ingin mencetak faktur untuk ${pelangganNama} (${kodePembayaran})?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Cetak!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.open(`{{ url('/pembayarans') }}/${pembayaranId}/invoice`, '_blank');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, pembayaran page ready...');

    // Show SweetAlert for session messages
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#10B981'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#EF4444'
        });
    @endif

    @if(session('show_invoice_option') && session('pembayaran_id'))
        Swal.fire({
            title: 'Cetak Faktur?',
            text: 'Pembayaran telah ditandai sebagai LUNAS. Apakah Anda ingin mencetak faktur?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Cetak!',
            cancelButtonText: 'Nanti Saja'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open('/pembayarans/{{ session('pembayaran_id') }}/invoice', '_blank');
            }
        });
    @endif
});
</script>
@endpush
@endsection
