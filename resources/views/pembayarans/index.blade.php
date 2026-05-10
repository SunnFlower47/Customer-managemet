@extends('layouts.app')

@section('title', 'Pembayaran - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg relative">
                <i class="fas fa-money-bill-wave text-white text-xl sm:text-2xl"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Data Pembayaran</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola tagihan dan status pembayaran pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            @if(in_array(auth()->user()?->role ?? 'guest', ['admin', 'operator']))
            <a href="{{ route('pembayarans.export', request()->query()) }}"
               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fas fa-file-pdf mr-2 text-xs sm:text-sm"></i>PDF
            </a>
            <a href="{{ route('pembayarans.export.excel', request()->query()) }}"
               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fas fa-file-excel mr-2 text-xs sm:text-sm"></i>Excel
            </a>
            <a href="{{ route('pembayarans.export.csv', request()->query()) }}"
               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fas fa-file-csv mr-2 text-xs sm:text-sm"></i>CSV
            </a>
            @endif
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-green-500"></i>Pembayaran auto-generate
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-search text-green-500"></i>Pencarian & Filter
            </h3>
            <p class="text-xs text-gray-500 flex items-center gap-1.5">
                <i class="fas fa-info-circle text-green-500"></i>Pilih kombinasi status, penagih, dan periode
            </p>
        </div>

        <form method="GET" action="{{ route('pembayarans.index') }}" class="space-y-5">
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-green-600"></i>Cari Pembayaran
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nama pelanggan, PPPoE, kode pembayaran..."
                        autocomplete="off"
                        class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm font-medium bg-gray-50 focus:bg-white">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <button type="submit" class="absolute inset-y-0 right-0 pr-4 flex items-center text-green-600 hover:text-green-700">
                        <i class="fas fa-arrow-right text-sm"></i>
                    </button>
                    <div id="search_suggestions"
                         class="absolute z-20 mt-2 w-full bg-white border border-gray-100 rounded-2xl shadow-lg hidden max-h-80 overflow-y-auto"
                         data-has-results="false">
                        <div class="py-3 text-center text-xs text-gray-400">Masukkan minimal 2 karakter</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-green-600"></i>Status
                    </label>
                    <select name="status" id="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div>
                    <label for="penagih_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tie mr-2 text-green-600"></i>Penagih
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="penagih_search"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium bg-gray-50 focus:bg-white"
                               placeholder="Cari penagih..."
                               autocomplete="off">
                        <input type="hidden" name="penagih_id" id="penagih_id" value="{{ request('penagih_id') }}">
                        <div id="penagih_dropdown" class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-lg hidden max-h-60 overflow-y-auto">
                            <div class="px-4 py-3 text-gray-500 cursor-pointer hover:bg-gray-50 font-medium" data-value="">
                                Semua Penagih
                            </div>
                            @if(isset($penagihs) && $penagihs->count() > 0)
                                @foreach($penagihs as $penagih)
                                    <div class="px-4 py-3 cursor-pointer hover:bg-gray-50 font-medium" data-value="{{ $penagih->id }}" data-name="{{ $penagih->nama }}">
                                        {{ $penagih->nama }}
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-3 text-gray-500 font-medium">
                                    Tidak ada penagih
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-green-600"></i>Bulan
                    </label>
                    <select name="bulan" id="bulan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-green-600"></i>Tahun
                    </label>
                    <select name="tahun" id="tahun" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Tahun</option>
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="inline-actions w-full">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-[1.01] transition">
                    <i class="fas fa-filter mr-2"></i>Terapkan
                </button>
                <a href="{{ route('pembayarans.index') }}" class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-data="bulkActions()" x-show="selected.length > 0 || applyAllUnpaidFiltered" x-cloak class="mt-8 app-card bg-green-50 border-2 border-green-200" id="bulkActionsBar" style="display: none;">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-sm font-semibold text-gray-700" x-show="!applyAllUnpaidFiltered">
                    <span x-text="selected.length"></span> item dipilih
                </span>
                <span class="text-sm font-semibold text-green-800" x-show="applyAllUnpaidFiltered">
                    Semua belum lunas sesuai filter terpilih ({{ number_format($unpaidFilteredCount ?? 0, 0, ',', '.') }} data)
                </span>
                <label class="inline-flex items-center gap-2 text-xs text-green-800 bg-green-100 border border-green-200 rounded-lg px-2 py-1 cursor-pointer">
                    <input type="checkbox" x-model="applyAllUnpaidFiltered" @change="toggleApplyAllFromSwitch($event)" class="rounded border-green-400 text-green-600 focus:ring-green-500">
                    Semua belum lunas (semua halaman sesuai filter)
                </label>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('pembayarans.bulk-mark-paid') }}" class="inline" id="bulkMarkPaidForm">
                    @csrf
                    <input type="hidden" name="ids" :value="JSON.stringify(selected)">
                    <input type="hidden" name="apply_all_unpaid_filtered" :value="applyAllUnpaidFiltered ? 1 : 0">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <button type="button" @click="confirmBulkMarkPaid($el.form)" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold transition">
                        <i class="fas fa-check-circle mr-2"></i>Tandai Lunas
                    </button>
                </form>
                <form method="POST" action="{{ route('pembayarans.bulk-update-status') }}" class="inline" id="bulkStatusForm">
                    @csrf
                    <input type="hidden" name="ids" :value="JSON.stringify(selected)">
                    <input type="hidden" name="apply_all_unpaid_filtered" :value="applyAllUnpaidFiltered ? 1 : 0">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="filter_status" value="{{ request('status') }}">
                    <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <select name="status" @change="confirmBulkStatusChange($event)" class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium bg-white">
                        <option value="">Ubah Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="belum_bayar">Belum Bayar</option>
                    </select>
                </form>
                <button type="button" @click="clearSelection()" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold transition">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="mt-8 app-card app-card--soft overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider w-12">
                            <input type="checkbox" name="select_all" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-barcode mr-2"></i>Kode Pembayaran
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Pelanggan
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user-tie mr-2"></i>Penagih
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Periode
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-money-bill mr-2"></i>Jumlah
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2"></i>Jatuh Tempo
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-clock mr-2"></i>Tanggal Bayar
                        </th>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pembayarans as $pembayaran)
                    <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200 border-b border-gray-100">
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" name="selected_ids" value="{{ $pembayaran->id }}" 
                                {{ $pembayaran->status === 'lunas' ? 'disabled' : '' }}
                                title="{{ $pembayaran->status === 'lunas' ? 'Pembayaran lunas tidak bisa dipilih untuk bulk lunas' : '' }}"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500 {{ $pembayaran->status === 'lunas' ? 'opacity-40 cursor-not-allowed' : '' }}">
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-xs font-mono text-blue-600 bg-blue-50 px-3 py-2 rounded-lg font-semibold">
                                {{ $pembayaran->kode_pembayaran }}
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg text-sm font-bold text-white">
                                        {{ substr($pembayaran->pelanggan->nama, 0, 1) }}
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-wifi"></i>{{ $pembayaran->pelanggan->pppoe }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-900">
                                <div class="flex items-center bg-blue-50 px-3 py-2 rounded-lg border border-blue-100">
                                    <i class="fas fa-user-tie mr-2 text-blue-600"></i>
                                    <span class="font-medium truncate max-w-[160px]">{{ $pembayaran->historical_collector_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-900 bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-100 font-semibold">
                                <i class="fas fa-calendar mr-1 text-yellow-600"></i>
                                {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-3 rounded-2xl border border-green-100">
                                <div class="text-base font-bold text-green-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</div>
                                @if($pembayaran->pelanggan->paket)
                                    <div class="text-xs text-green-700 font-semibold truncate">{{ $pembayaran->pelanggan->paket->nama_paket }}</div>
                                @else
                                    <div class="text-xs text-red-600 italic">Paket tidak ditemukan</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-bold
                                {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                <i class="fas fa-circle mr-1 text-[10px]"></i>{{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @php
                                $dueDate = \Carbon\Carbon::create($pembayaran->tahun_tagihan, $pembayaran->bulan_tagihan, $pembayaran->pelanggan->tanggal_pembayaran);
                                $isOverdue = $dueDate->isPast() && $pembayaran->status !== 'lunas';
                            @endphp
                            <div class="text-xs text-gray-900 {{ $isOverdue ? 'bg-red-50 px-3 py-2 rounded-lg border border-red-200' : 'bg-gray-50 px-3 py-2 rounded-lg' }}">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-calendar-alt mr-1 {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}"></i>
                                    <span class="{{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-900 font-semibold' }}">
                                        {{ $dueDate->format('d M Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <i class="fas fa-exclamation-triangle text-red-500 ml-2" title="Jatuh tempo"></i>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-900">
                                @if($pembayaran->tanggal_bayar)
                                    <div class="bg-green-50 px-3 py-2 rounded-lg border border-green-100 flex items-center gap-2">
                                        <i class="fas fa-check-circle mr-1 text-green-600"></i>
                                        <span class="font-semibold text-green-900">{{ $pembayaran->tanggal_bayar->format('d M Y H:i') }}</span>
                                    </div>
                                @else
                                    <div class="bg-gray-50 px-3 py-2 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-clock mr-1 text-gray-400"></i>
                                        <span class="text-gray-500 font-medium">-</span>
                                    </div>

                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-medium">
                            <div class="inline-flex flex-wrap justify-center gap-2">
                                <!-- Tombol Detail -->
                                <a href="{{ route('pembayarans.show', $pembayaran) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>

                                <!-- Tombol Edit -->
                                @can('edit-pembayaran')
                                <a href="{{ route('pembayarans.edit', array_merge([$pembayaran], request()->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']))) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition"
                                   title="Edit Pembayaran">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                @endcan

                                <!-- Tombol Status -->
                                <button type="button"
                                        class="inline-flex items-center px-3.5 py-2 text-[12px] {{ $pembayaran->status === 'belum_bayar' ? 'bg-gradient-to-r from-green-500 to-green-600 hover:shadow-lg' : 'bg-gradient-to-r from-yellow-500 to-yellow-600 hover:shadow-lg' }} text-white rounded-xl hover:scale-105 transition"
                                        data-pembayaran-id="{{ $pembayaran->id }}"
                                        data-current-status="{{ $pembayaran->status }}"
                                        data-pelanggan-nama="{{ $pembayaran->pelanggan->nama }}"
                                        onclick="updateStatus({{ $pembayaran->id }}, '{{ $pembayaran->status }}', '{{ $pembayaran->pelanggan->nama }}')"
                                        title="{{ $pembayaran->status === 'belum_bayar' ? 'Tandai sebagai Lunas' : 'Ubah ke Belum Bayar' }}">
                                    @if($pembayaran->status === 'belum_bayar')
                                        <i class="fas fa-check mr-2"></i>Lunas
                                    @else
                                        <i class="fas fa-undo mr-2"></i>Batal
                                    @endif
                                </button>

                                <!-- Tombol Cetak Faktur (hanya untuk status LUNAS) -->
                                @if($pembayaran->status === 'lunas')
                                <button type="button"
                                        class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition"
                                        data-pembayaran-id="{{ $pembayaran->id }}"
                                        data-kode-pembayaran="{{ $pembayaran->kode_pembayaran }}"
                                        data-pelanggan-nama="{{ $pembayaran->pelanggan->nama }}"
                                        onclick="printInvoice({{ $pembayaran->id }}, '{{ $pembayaran->kode_pembayaran }}', '{{ $pembayaran->pelanggan->nama }}')"
                                        title="Cetak Faktur">
                                    <i class="fas fa-print mr-2"></i>Faktur
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-receipt text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada pembayaran</h3>
                                <p class="text-gray-500 text-lg mb-6">Belum ada data pembayaran yang ditemukan.</p>
                                <a href="{{ route('pembayarans.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                                    <i class="fas fa-refresh mr-2"></i>Refresh Data
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-3">
            @forelse($pembayarans as $pembayaran)
            <div class="mobile-card bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center gap-3 mb-3">
                    <input type="checkbox" name="selected_ids" value="{{ $pembayaran->id }}" 
                        {{ $pembayaran->status === 'lunas' ? 'disabled' : '' }}
                        title="{{ $pembayaran->status === 'lunas' ? 'Pembayaran lunas tidak bisa dipilih untuk bulk lunas' : '' }}"
                        class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500 {{ $pembayaran->status === 'lunas' ? 'opacity-40 cursor-not-allowed' : '' }}">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-md text-sm font-bold text-white">
                        {{ substr($pembayaran->pelanggan->nama, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-base font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</div>
                        <div class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-phone"></i>{{ $pembayaran->pelanggan->no_hp }}
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold ml-auto
                        @if($pembayaran->status === 'lunas') bg-green-100 text-green-800 border border-green-200
                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                        <i class="fas fa-circle mr-1 text-[9px]"></i>{{ strtoupper($pembayaran->status === 'lunas' ? 'Lunas' : 'Belum') }}
                    </span>
                </div>

                <div class="space-y-2 text-xs text-gray-600">
                    <div class="bg-gray-50 px-3 py-2 rounded-xl flex items-center justify-between">
                        <span class="font-semibold text-gray-800">Kode</span>
                        <span class="font-mono text-blue-600 text-[11px] break-all ml-3">{{ $pembayaran->kode_pembayaran }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-blue-50 px-3 py-2 rounded-xl border border-blue-100">
                            <span class="font-semibold text-blue-800 block text-[11px]">Penagih</span>
                            <span class="text-blue-900 font-semibold line-clamp-1">{{ $pembayaran->historical_collector_name }}</span>
                        </div>
                        <div class="bg-yellow-50 px-3 py-2 rounded-xl border border-yellow-100">
                            <span class="font-semibold text-yellow-800 block text-[11px]">Periode</span>
                            <span class="text-yellow-900 font-semibold">{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-3 py-2 rounded-xl border border-green-100 text-center">
                        <span class="text-[11px] font-semibold text-green-700">Jumlah</span>
                        <p class="text-lg font-bold text-green-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @php
                            $dueDate = \Carbon\Carbon::create($pembayaran->tahun_tagihan, $pembayaran->bulan_tagihan, $pembayaran->pelanggan->tanggal_pembayaran);
                            $isOverdue = $dueDate->isPast() && $pembayaran->status !== 'lunas';
                        @endphp
                        <div class="bg-gray-50 px-3 py-2 rounded-xl">
                            <span class="font-semibold text-gray-800 text-[11px]">Jatuh Tempo</span>
                            <div class="{{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-900 font-semibold' }}">
                                {{ $dueDate->format('d/m/Y') }}
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-triangle text-red-500 ml-1"></i>
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 px-3 py-2 rounded-xl">
                            <span class="font-semibold text-gray-800 text-[11px]">Tanggal Bayar</span>
                            <div class="text-gray-900 font-semibold">
                                {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-2 text-[11px] font-semibold">
                    <a href="{{ route('pembayarans.show', $pembayaran) }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mr-1.5"></i>Detail
                    </a>
                    @can('edit-pembayaran')
                    <a href="{{ route('pembayarans.edit', array_merge([$pembayaran], request()->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']))) }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-edit mr-1.5"></i>Edit
                    </a>
                    @endcan
                    @if($pembayaran->status === 'lunas')
                    <a href="{{ route('pembayarans.invoice', $pembayaran) }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-print mr-1.5"></i>Faktur
                    </a>
                    @endif
                    <button onclick="updateStatus({{ $pembayaran->id }}, '{{ $pembayaran->status }}', '{{ $pembayaran->pelanggan->nama }}')"
                            class="inline-flex items-center justify-center px-3 py-2 {{ $pembayaran->status === 'lunas' ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-green-500 to-green-600' }} text-white rounded-xl hover:shadow-md transition">
                        <i class="fas {{ $pembayaran->status === 'lunas' ? 'fa-undo' : 'fa-check' }} mr-1.5"></i>
                        {{ $pembayaran->status === 'lunas' ? 'Batal' : 'Lunas' }}
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-receipt text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada pembayaran</h3>
                <p class="text-gray-500 text-lg mb-6">Belum ada data pembayaran yang ditemukan.</p>
                <a href="{{ route('pembayarans.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                    <i class="fas fa-refresh mr-2"></i>Refresh Data
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 app-card">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-xs sm:text-sm text-gray-600">
                Menampilkan <span class="font-semibold text-gray-900">{{ $pembayarans->firstItem() ?? 0 }}</span> -
                <span class="font-semibold text-gray-900">{{ $pembayarans->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-900">{{ $pembayarans->total() }}</span> pembayaran
            </div>
            <div class="flex items-center justify-center sm:justify-end">
                {{ $pembayarans->appends(request()->query())->onEachSide(1)->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Ensure SweetAlert is loaded
if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 not loaded!');
}

// Searchable Penagih Dropdown + Realtime pembayaran search
document.addEventListener('DOMContentLoaded', function() {
    const debounce = (fn, delay = 300) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    };

    // Realtime pembayaran search suggestions
    const pembayaranSearchInput = document.getElementById('search');
    const suggestionPanel = document.getElementById('search_suggestions');
    const suggestionUrl = "{{ route('pembayarans.suggestions') }}";
    let activeSuggestionAbort = null;

    function hideSuggestions() {
        if (!suggestionPanel) return;
        suggestionPanel.classList.add('hidden');
        suggestionPanel.dataset.hasResults = 'false';
    }

    function renderSuggestions(items) {
        if (!suggestionPanel) return;
        if (!items.length) {
            suggestionPanel.innerHTML = '<div class="py-4 text-center text-xs text-gray-400">Tidak ditemukan pembayaran</div>';
            suggestionPanel.dataset.hasResults = 'false';
            suggestionPanel.classList.remove('hidden');
            return;
        }

        suggestionPanel.innerHTML = items.map(item => `
            <button type="button"
                    class="w-full text-left px-4 py-3 flex flex-col gap-1 hover:bg-gray-50 focus:bg-gray-50 transition rounded-2xl"
                    data-url="${item.detail_url}"
                    data-label="${item.kode}">
                <div class="flex items-center justify-between gap-3">
                    <span class="font-semibold text-sm text-gray-900 truncate">${item.kode}</span>
                    <span class="text-[11px] font-semibold ${item.status === 'lunas' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'} px-2 py-0.5 rounded-full border ${item.status === 'lunas' ? 'border-green-100' : 'border-red-100'}">
                        ${item.status.toUpperCase()}
                    </span>
                </div>
                <div class="text-[11px] text-gray-500 truncate">
                    ${item.pelanggan ? `<i class="fas fa-user mr-1 text-[10px]"></i>${item.pelanggan}` : ''}
                    ${item.pppoe ? `<span class="ml-2 font-mono">${item.pppoe}</span>` : ''}
                </div>
                <div class="text-[11px] text-gray-400 flex items-center justify-between">
                    <span><i class="fas fa-calendar mr-1"></i>${item.periode}</span>
                    <span class="text-gray-900 font-semibold">Rp ${item.jumlah}</span>
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
            suggestionPanel.innerHTML = '<div class="py-4 text-center text-xs text-red-400">Tidak bisa memuat saran</div>';
            suggestionPanel.classList.remove('hidden');
        }
    }

    if (pembayaranSearchInput && suggestionPanel) {
        pembayaranSearchInput.setAttribute('autocomplete', 'off');

        const debouncedFetch = debounce((term) => {
            if (term.length < 2) {
                hideSuggestions();
                return;
            }
            fetchSuggestions(term);
        }, 350);

        pembayaranSearchInput.addEventListener('input', (event) => {
            debouncedFetch(event.target.value.trim());
        });

        pembayaranSearchInput.addEventListener('focus', () => {
            if (suggestionPanel.dataset.hasResults === 'true') {
                suggestionPanel.classList.remove('hidden');
            }
        });

        suggestionPanel.addEventListener('mousedown', (e) => e.preventDefault());
        suggestionPanel.addEventListener('click', (e) => {
            const item = e.target.closest('button[data-url]');
            if (!item) return;
            pembayaranSearchInput.value = item.dataset.label;
            hideSuggestions();
            window.location.href = item.dataset.url;
        });

        document.addEventListener('click', (event) => {
            if (!suggestionPanel.contains(event.target) && event.target !== pembayaranSearchInput) {
                hideSuggestions();
            }
        });

        pembayaranSearchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                hideSuggestions();
                pembayaranSearchInput.blur();
            }
        });
    }

    const penagihSearchInput = document.getElementById('penagih_search');
    const hiddenInput = document.getElementById('penagih_id');
    const dropdown = document.getElementById('penagih_dropdown');

    if (penagihSearchInput && hiddenInput && dropdown) {
        const allOptions = dropdown.querySelectorAll('[data-value]');
        let clickTimeout = null;

        const selectedValue = hiddenInput.value;
        if (selectedValue) {
            const selectedOption = dropdown.querySelector(`[data-value="${selectedValue}"]`);
            if (selectedOption) {
                penagihSearchInput.value = selectedOption.dataset.name || selectedOption.textContent.trim();
            }
        }

        function showDropdown() {
            dropdown.classList.remove('hidden');
            filterOptions();
        }

        function hideDropdown() {
            dropdown.classList.add('hidden');
        }

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
            e.preventDefault();
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

});

// Bulk Actions Handler
function bulkActions() {
    return {
        selected: [],
        selectAll: false,
        applyAllUnpaidFiltered: false,
        unpaidFilteredCount: {{ (int)($unpaidFilteredCount ?? 0) }},
        storageKey: 'pembayarans_bulk_selected_ids',
        storageApplyAllKey: 'pembayarans_bulk_apply_all_unpaid_filtered',
        init() {
            this.loadSelection();
            this.loadApplyAllFlag();
            this.syncCheckboxes();
            this.updateBulkBar();

            // Listen for checkbox changes
            document.addEventListener('change', (e) => {
                if (e.target.matches('input[type=checkbox][name=selected_ids]')) {
                    const id = parseInt(e.target.value);
                    if (e.target.checked) {
                        if (!this.selected.includes(id)) {
                            this.selected.push(id);
                        }
                    } else {
                        this.selected = this.selected.filter(selectedId => selectedId !== id);
                    }
                    this.saveSelection();
                    if (!e.target.checked) {
                        this.applyAllUnpaidFiltered = false;
                        this.saveApplyAllFlag();
                    }
                    this.syncSelectAllState();
                    this.updateBulkBar();
                }
                if (e.target.matches('input[type=checkbox][name=select_all]')) {
                    this.selectAll = e.target.checked;
                    const pageIds = Array.from(document.querySelectorAll('input[type=checkbox][name=selected_ids]:not(:disabled)')).map(cb => parseInt(cb.value));

                    if (this.selectAll) {
                        pageIds.forEach(id => {
                            if (!this.selected.includes(id)) this.selected.push(id);
                        });
                        this.applyAllUnpaidFiltered = true;
                    } else {
                        this.selected = this.selected.filter(id => !pageIds.includes(id));
                        this.applyAllUnpaidFiltered = false;
                    }
                    this.saveApplyAllFlag();

                    document.querySelectorAll('input[type=checkbox][name=selected_ids]:not(:disabled)').forEach(cb => {
                        cb.checked = this.selectAll;
                    });

                    this.saveSelection();
                    this.updateBulkBar();
                }
            });
        },
        loadSelection() {
            try {
                const raw = localStorage.getItem(this.storageKey);
                this.selected = raw ? JSON.parse(raw).map(Number).filter(Number.isFinite) : [];
            } catch (_) {
                this.selected = [];
            }
        },
        saveSelection() {
            localStorage.setItem(this.storageKey, JSON.stringify(this.selected));
        },
        loadApplyAllFlag() {
            try {
                this.applyAllUnpaidFiltered = localStorage.getItem(this.storageApplyAllKey) === '1';
            } catch (_) {
                this.applyAllUnpaidFiltered = false;
            }
        },
        saveApplyAllFlag() {
            localStorage.setItem(this.storageApplyAllKey, this.applyAllUnpaidFiltered ? '1' : '0');
        },
        syncCheckboxes() {
            const enabled = Array.from(document.querySelectorAll('input[type=checkbox][name=selected_ids]:not(:disabled)'));

            if (this.applyAllUnpaidFiltered) {
                enabled.forEach(cb => {
                    const id = parseInt(cb.value);
                    cb.checked = true;
                    if (!this.selected.includes(id)) this.selected.push(id);
                });
                this.saveSelection();
            } else {
                enabled.forEach(cb => {
                    cb.checked = this.selected.includes(parseInt(cb.value));
                });
            }

            this.syncSelectAllState();
        },
        syncSelectAllState() {
            const pageCheckboxes = Array.from(document.querySelectorAll('input[type=checkbox][name=selected_ids]:not(:disabled)'));
            const selectAllCheckbox = document.querySelector('input[type=checkbox][name=select_all]');
            if (!selectAllCheckbox) return;
            this.selectAll = pageCheckboxes.length > 0 && pageCheckboxes.every(cb => this.selected.includes(parseInt(cb.value)));
            selectAllCheckbox.checked = this.selectAll;
        },
        updateBulkBar() {
            const bar = document.getElementById('bulkActionsBar');
            if (this.selected.length > 0 || this.applyAllUnpaidFiltered) {
                bar.style.display = 'block';
            } else {
                bar.style.display = 'none';
            }
        },
        toggleApplyAllFromSwitch(event) {
            this.applyAllUnpaidFiltered = event.target.checked;
            if (this.applyAllUnpaidFiltered) {
                this.selectAllAcrossPages();
            } else {
                this.saveApplyAllFlag();
                this.syncSelectAllState();
                this.updateBulkBar();
            }
        },
        selectAllAcrossPages() {
            const enabled = Array.from(document.querySelectorAll('input[type=checkbox][name=selected_ids]:not(:disabled)'));
            enabled.forEach(cb => {
                cb.checked = true;
                const id = parseInt(cb.value);
                if (!this.selected.includes(id)) this.selected.push(id);
            });
            this.selectAll = true;
            this.applyAllUnpaidFiltered = true;
            this.saveSelection();
            this.saveApplyAllFlag();
            this.syncSelectAllState();
            this.updateBulkBar();
        },
        confirmBulkMarkPaid(formEl) {
            const totalTarget = this.applyAllUnpaidFiltered ? this.unpaidFilteredCount : this.selected.length;
            if (!totalTarget) return;

            Swal.fire({
                title: 'Konfirmasi Bulk Lunas',
                text: `Yakin ingin menandai ${totalTarget} pembayaran sebagai lunas?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16A34A',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    formEl.submit();
                }
            });
        },
        confirmBulkStatusChange(event) {
            const selectedStatus = event.target.value;
            const totalTarget = this.applyAllUnpaidFiltered ? this.unpaidFilteredCount : this.selected.length;
            if (!selectedStatus || !totalTarget) return;

            const statusLabel = selectedStatus === 'lunas' ? 'Lunas' : 'Belum Bayar';

            Swal.fire({
                title: 'Konfirmasi Bulk Update',
                text: `Yakin ingin mengubah ${totalTarget} pembayaran menjadi ${statusLabel}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16A34A',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.form.submit();
                } else {
                    event.target.value = '';
                }
            });
        },
        clearSelection() {
            this.selected = [];
            this.selectAll = false;
            this.applyAllUnpaidFiltered = false;
            localStorage.removeItem(this.storageKey);
            localStorage.removeItem(this.storageApplyAllKey);
            document.querySelectorAll('input[type=checkbox][name=selected_ids]').forEach(cb => {
                cb.checked = false;
            });
            const selectAllCheckbox = document.querySelector('input[type=checkbox][name=select_all]');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
            this.updateBulkBar();
        }
    }
}
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

@if(session('show_invoice_option') && session('pembayaran_id'))
<script>
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
</script>
@endif
@endpush
@endsection


