@extends('layouts.app')

@section('title', 'Laporan Pendapatan - {{ \App\Models\CompanyProfile::first()->initials ?? "BCM" }} WiFi Billing')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Laporan Pendapatan</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Laporan pendapatan dari pembayaran pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-green-500"></i>Analisis pendapatan
            </div>
        </div>
    </div>

    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-green-500"></i>Filter Laporan
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-green-500"></i>
                Pilih periode untuk analisis pendapatan
            </p>
        </div>
        <form method="GET" action="{{ route('laporan.pendapatan') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2 text-green-500"></i>Tahun
                </label>
                <select name="tahun" id="tahun" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-green-500"></i>Bulan
                </label>
                <select name="bulan" id="bulan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-filter mr-2"></i>Filter Laporan
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Total Pendapatan</p>
                    <p class="stat-card__value text-gray-900">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-blue-100 text-blue-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Pembayaran Lunas</p>
                    <p class="stat-card__value text-gray-900">{{ $summary['pembayaran_lunas'] }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Belum Lunas</p>
                    <p class="stat-card__value text-gray-900">{{ $summary['pembayaran_belum_lunas'] }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-purple-100 text-purple-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Total Pelanggan</p>
                    <p class="stat-card__value text-gray-900">{{ $summary['total_pelanggan'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card space-y-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Grafik Pendapatan</p>
            <h2 class="text-base font-semibold text-gray-900">Pendapatan bulanan</h2>
        </div>
        <div class="h-48 sm:h-64 flex items-end justify-between gap-1 sm:gap-2 relative overflow-hidden">
            @foreach($chartData as $data)
                <div class="flex flex-col items-center flex-1 min-w-0">
                    <div class="w-full bg-gradient-to-t from-green-500 to-green-400 rounded-t-xl relative group" style="height: {{ $data['height'] }}px; max-height: 180px;">
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
                <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Detail Pembayaran</p>
                <h2 class="text-base font-semibold text-gray-900">Daftar pembayaran</h2>
            </div>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-500 to-green-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Pelanggan</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Penagih</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Bulan/Tahun</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Jumlah</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayarans as $pembayaran)
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                                    <p class="text-xs text-gray-500 font-mono truncate">{{ \Illuminate\Support\Str::limit($pembayaran->pelanggan->pppoe, 20) }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700 truncate max-w-xs">
                                    @if($pembayaran->pelanggan->penagih)
                                        {{ $pembayaran->pelanggan->penagih->nama }}
                                    @else
                                        <span class="text-gray-400 italic">Belum ada penagih</span>
                                    @endif
                                </p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700">
                                    {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->translatedFormat('M') }} {{ $pembayaran->tahun_tagihan }}
                                </p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($pembayaran->status == 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1 text-[9px]"></i>Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-clock mr-1 text-[9px]"></i>Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700">
                                    @if($pembayaran->tanggal_bayar)
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada data pembayaran</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($pembayarans->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $pembayarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        <div class="lg:hidden space-y-2">
            @forelse($pembayarans as $pembayaran)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white text-xs font-bold flex items-center justify-center shadow">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                        <p class="text-xs text-gray-500 font-mono truncate">{{ \Illuminate\Support\Str::limit($pembayaran->pelanggan->pppoe, 30) }}</p>
                    </div>
                    @if($pembayaran->status == 'lunas')
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200 ml-auto">
                            Lunas
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200 ml-auto">
                            Belum Lunas
                        </span>
                    @endif
                </div>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Penagih:</span>
                        <span class="text-gray-900 truncate">{{ $pembayaran->pelanggan->penagih ? $pembayaran->pelanggan->penagih->nama : 'Belum ada penagih' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-semibold">Periode:</span>
                            <span class="text-gray-900">{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->translatedFormat('M') }} {{ $pembayaran->tahun_tagihan }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Jumlah:</span>
                            <span class="text-gray-900 font-semibold">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @if($pembayaran->tanggal_bayar)
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Tanggal Bayar:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="app-card text-center py-12">
                <div class="flex flex-col items-center">
                    <i class="fas fa-chart-line text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada data</h3>
                    <p class="text-sm text-gray-500">Belum ada data pembayaran yang ditemukan.</p>
                </div>
            </div>
            @endforelse

            @if($pembayarans->hasPages())
                <div class="mt-4">
                    {{ $pembayarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection
