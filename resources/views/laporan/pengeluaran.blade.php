@extends('layouts.app')

@section('title', 'Laporan Pengeluaran')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-chart-area"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Laporan Pengeluaran</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Laporan pengeluaran operasional perusahaan</p>
            </div>
        </div>
        <div class="page-header__actions">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-red-500"></i>Analisis pengeluaran
            </div>
        </div>
    </div>

    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-red-500"></i>Filter Laporan
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-red-500"></i>
                Pilih periode untuk analisis pengeluaran
            </p>
        </div>
        <form method="GET" action="{{ route('laporan.pengeluaran') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2 text-red-500"></i>Tahun
                </label>
                <select name="tahun" id="tahun" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-red-500"></i>Bulan
                </label>
                <select name="bulan" id="bulan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-filter mr-2"></i>Filter Laporan
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-red-100 text-red-600">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Total Pengeluaran</p>
                    <p class="stat-card__value text-gray-900">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-blue-100 text-blue-600">
                    <i class="fas fa-list"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Jumlah Transaksi</p>
                    <p class="stat-card__value text-gray-900">{{ $summary['total_transaksi'] }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-green-100 text-green-600">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Rata-rata per Transaksi</p>
                    <p class="stat-card__value text-gray-900">Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card space-y-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-red-500 font-semibold">Grafik Pengeluaran</p>
            <h2 class="text-base font-semibold text-gray-900">Pengeluaran bulanan</h2>
        </div>
        <div class="h-48 sm:h-64 flex items-end justify-between gap-1 sm:gap-2 relative overflow-hidden">
            @foreach($chartData as $data)
                <div class="flex flex-col items-center flex-1 min-w-0">
                    <div class="w-full bg-gradient-to-t from-red-500 to-red-400 rounded-t-xl relative group" style="height: {{ $data['height'] }}px; max-height: 180px;">
                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10 whitespace-nowrap">
                            Rp {{ number_format($data['amount'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[10px] sm:text-xs text-gray-600 mt-2 truncate w-full text-center">{{ $data['month'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-red-500 font-semibold">Detail Pengeluaran</p>
                <h2 class="text-base font-semibold text-gray-900">Daftar pengeluaran</h2>
            </div>
            @if((auth()->user()?->role ?? 'guest') === 'admin')
                <a href="{{ route('pengeluarans.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah Pengeluaran
                </a>
            @endif
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-red-500 to-red-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Deskripsi</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Jumlah</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengeluarans as $pengeluaran)
                        <tr class="hover:bg-red-50 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm text-gray-900 truncate max-w-xs">{{ $pengeluaran->deskripsi }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700 truncate max-w-xs">{{ $pengeluaran->user->name }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs font-medium">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('pengeluarans.show', $pengeluaran) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if((auth()->user()?->role ?? 'guest') === 'admin')
                                        <a href="{{ route('pengeluarans.edit', $pengeluaran) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada data pengeluaran</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($pengeluarans->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $pengeluarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        <div class="lg:hidden space-y-2">
            @forelse($pengeluarans as $pengeluaran)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 text-white flex items-center justify-center shadow">
                        <i class="fas fa-receipt text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $pengeluaran->deskripsi }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-base font-bold text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Oleh: {{ $pengeluaran->user->name }}</p>
                    </div>
                </div>
                <div class="mt-2 flex justify-end gap-2 text-xs">
                    <a href="{{ route('pengeluarans.show', $pengeluaran) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                        <i class="fas fa-eye mr-1"></i>Lihat
                    </a>
                    @if((auth()->user()?->role ?? 'guest') === 'admin')
                        <a href="{{ route('pengeluarans.edit', $pengeluaran) }}" class="text-yellow-600 hover:text-yellow-900 font-semibold">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="app-card text-center py-12">
                <div class="flex flex-col items-center">
                    <i class="fas fa-chart-area text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada data</h3>
                    <p class="text-sm text-gray-500">Belum ada data pengeluaran yang ditemukan.</p>
                </div>
            </div>
            @endforelse

            @if($pengeluarans->hasPages())
                <div class="mt-4">
                    {{ $pengeluarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection
