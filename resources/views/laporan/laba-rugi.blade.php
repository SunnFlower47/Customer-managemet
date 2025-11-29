@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Laporan Laba Rugi</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Laporan laba rugi perusahaan berdasarkan pendapatan dan pengeluaran</p>
            </div>
        </div>
        <div class="page-header__actions">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-emerald-500"></i>Analisis keuangan
            </div>
        </div>
    </div>

    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-emerald-500"></i>Filter Laporan
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-emerald-500"></i>
                Pilih periode untuk analisis keuangan
            </p>
        </div>
        <form method="GET" action="{{ route('laporan.laba-rugi') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2 text-emerald-500"></i>Tahun
                </label>
                <select name="tahun" id="tahun" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-emerald-500"></i>Bulan
                </label>
                <select name="bulan" id="bulan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-filter mr-2"></i>Filter Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    @php
        $summary = $summary ?? [
            'total_pendapatan' => 0,
            'total_pengeluaran' => 0,
            'laba_rugi' => 0,
            'margin_percentage' => 0
        ];

        $chartData = $chartData ?? [];
        $maxValue = 1;
        if (!empty($chartData)) {
            $maxPendapatan = max(array_column($chartData, 'pendapatan'));
            $maxPengeluaran = max(array_column($chartData, 'pengeluaran'));
            $maxValue = max($maxPendapatan, $maxPengeluaran, 1);
        }

        // Generate points for SVG polyline
        $pendapatanPoints = '';
        $pengeluaranPoints = '';
        $labaRugiPoints = '';
        if (!empty($chartData)) {
            $count = count($chartData);
            $maxLabaRugi = 1;

            // Find max laba rugi for scaling
            foreach ($chartData as $data) {
                $maxLabaRugi = max($maxLabaRugi, abs($data['laba_rugi'] ?? 0));
            }

            foreach ($chartData as $index => $data) {
                $x = $index * (100 / ($count - 1));
                $pendapatanY = 100 - (($data['pendapatan'] ?? 0) / $maxValue * 80);
                $pengeluaranY = 100 - (($data['pengeluaran'] ?? 0) / $maxValue * 80);

                // For laba rugi, center at 50 (middle) and scale based on value
                $labaRugiY = 50 - (($data['laba_rugi'] ?? 0) / $maxLabaRugi * 40);

                $pendapatanPoints .= $x . ',' . $pendapatanY;
                $pengeluaranPoints .= $x . ',' . $pengeluaranY;
                $labaRugiPoints .= $x . ',' . $labaRugiY;

                if ($index < $count - 1) {
                    $pendapatanPoints .= ' ';
                    $pengeluaranPoints .= ' ';
                    $labaRugiPoints .= ' ';
                }
            }
        }
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-green-100 text-green-600">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Total Pendapatan</p>
                    <p class="stat-card__value text-green-600">Rp {{ number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-red-100 text-red-600">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Total Pengeluaran</p>
                    <p class="stat-card__value text-red-600">Rp {{ number_format($summary['total_pengeluaran'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon {{ ($summary['laba_rugi'] ?? 0) >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Laba/Rugi</p>
                    <p class="stat-card__value {{ ($summary['laba_rugi'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($summary['laba_rugi'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="stat-card app-card">
            <div class="flex items-center gap-3">
                <div class="stat-card__icon bg-blue-100 text-blue-600">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-600">Margin (%)</p>
                    <p class="stat-card__value text-blue-600">{{ number_format($summary['margin_percentage'] ?? 0, 2) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pendapatan vs Pengeluaran Chart -->
        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-emerald-500 font-semibold">Grafik Perbandingan</p>
                <h2 class="text-base font-semibold text-gray-900">Pendapatan vs Pengeluaran</h2>
                <p class="text-xs text-gray-500 mt-1">Perbandingan bulanan</p>
            </div>
            <div class="h-56 sm:h-72 lg:h-96 relative overflow-hidden">
                <div class="relative w-full h-full">
                    <!-- Y-axis labels -->
                    <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-500 pr-3 w-16">
                        @php
                            $chartData = $chartData ?? [];
                            $maxValue = $maxValue ?? max(
                                collect($chartData)->max('pendapatan') ?? 0,
                                collect($chartData)->max('pengeluaran') ?? 0
                            );
                        @endphp
                        <span class="font-semibold text-right">Rp {{ number_format($maxValue * 0.8, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">Rp {{ number_format($maxValue * 0.6, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">Rp {{ number_format($maxValue * 0.4, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">Rp {{ number_format($maxValue * 0.2, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">Rp 0</span>
                    </div>

                    <!-- Chart area -->
                    <div class="ml-16 mr-2 h-full relative">
                        <!-- Grid lines -->
                        <div class="absolute inset-0 grid grid-rows-4 gap-0">
                            <div class="border-t border-gray-200"></div>
                            <div class="border-t border-gray-200"></div>
                            <div class="border-t border-gray-200"></div>
                            <div class="border-t border-gray-200"></div>
                        </div>

                        <!-- Line chart -->
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <!-- Pendapatan line -->
                            <polyline
                                points="{{ $pendapatanPoints }}"
                                fill="none"
                                stroke="url(#pendapatanGradient)"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="drop-shadow-lg"
                            />

                            <!-- Pengeluaran line -->
                            <polyline
                                points="{{ $pengeluaranPoints }}"
                                fill="none"
                                stroke="url(#pengeluaranGradient)"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="drop-shadow-lg"
                            />

                            <!-- Data points -->
                            @foreach($chartData as $index => $data)
                                <circle
                                    cx="{{ $index * (100 / (count($chartData) - 1)) }}"
                                    cy="{{ 100 - ($data['pendapatan'] / $maxValue * 80) }}"
                                    r="2"
                                    fill="#10B981"
                                    class="hover:r-3 transition-all duration-200 cursor-pointer"
                                >
                                    <title>Pendapatan: Rp {{ number_format($data['pendapatan'], 0, ',', '.') }}</title>
                                </circle>
                                <circle
                                    cx="{{ $index * (100 / (count($chartData) - 1)) }}"
                                    cy="{{ 100 - ($data['pengeluaran'] / $maxValue * 80) }}"
                                    r="2"
                                    fill="#EF4444"
                                    class="hover:r-3 transition-all duration-200 cursor-pointer"
                                >
                                    <title>Pengeluaran: Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</title>
                                </circle>
                            @endforeach

                            <!-- Gradients -->
                            <defs>
                                <linearGradient id="pendapatanGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="pengeluaranGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#EF4444;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#DC2626;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>

                    <!-- X-axis labels -->
                    <div class="absolute bottom-0 left-16 right-2 flex justify-between text-xs text-gray-600 pb-1">
                        @foreach($chartData as $data)
                            <span class="font-semibold text-center">{{ $data['month'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-center gap-6 pt-2">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-green-400 rounded"></div>
                    <span class="text-xs font-semibold text-gray-600">Pendapatan</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-400 rounded"></div>
                    <span class="text-xs font-semibold text-gray-600">Pengeluaran</span>
                </div>
            </div>
        </div>

        <!-- Laba Rugi Trend Chart -->
        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Grafik Trend</p>
                <h2 class="text-base font-semibold text-gray-900">Trend Laba/Rugi</h2>
                <p class="text-xs text-gray-500 mt-1">Perkembangan keuntungan/kerugian</p>
            </div>
            <div class="h-56 sm:h-72 lg:h-96 relative overflow-hidden">
                <div class="relative w-full h-full">
                    <!-- Y-axis labels -->
                    <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-500 pr-3 w-16">
                        @php
                            $maxLabaRugi = 1;
                            if (!empty($chartData)) {
                                foreach ($chartData as $data) {
                                    $maxLabaRugi = max($maxLabaRugi, abs($data['laba_rugi'] ?? 0));
                                }
                            }
                        @endphp
                        <span class="font-semibold text-right">+Rp {{ number_format($maxLabaRugi, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">+Rp {{ number_format($maxLabaRugi * 0.5, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">Rp 0</span>
                        <span class="font-semibold text-right">-Rp {{ number_format($maxLabaRugi * 0.5, 0, ',', '.') }}</span>
                        <span class="font-semibold text-right">-Rp {{ number_format($maxLabaRugi, 0, ',', '.') }}</span>
                    </div>

                    <!-- Chart area -->
                    <div class="ml-16 mr-2 h-full relative">
                        <!-- Grid lines -->
                        <div class="absolute inset-0 grid grid-rows-4 gap-0">
                            <div class="border-t border-gray-200"></div>
                            <div class="border-t border-gray-200"></div>
                            <div class="border-t border-gray-200 border-dashed"></div>
                            <div class="border-t border-gray-200"></div>
                        </div>

                        <!-- Line chart -->
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <!-- Zero line -->
                            <line x1="0" y1="50" x2="100" y2="50" stroke="#6B7280" stroke-width="1" stroke-dasharray="2,2" opacity="0.5" />

                            <!-- Laba Rugi line -->
                            <polyline
                                points="{{ $labaRugiPoints }}"
                                fill="none"
                                stroke="url(#labaRugiGradient)"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="drop-shadow-lg"
                            />

                            <!-- Data points -->
                            @foreach($chartData as $index => $data)
                                <circle
                                    cx="{{ $index * (100 / (count($chartData) - 1)) }}"
                                    cy="{{ 50 - (($data['laba_rugi'] ?? 0) / $maxLabaRugi * 40) }}"
                                    r="2"
                                    fill="{{ ($data['laba_rugi'] ?? 0) >= 0 ? '#10B981' : '#EF4444' }}"
                                    class="hover:r-3 transition-all duration-200 cursor-pointer"
                                >
                                    <title>{{ ($data['laba_rugi'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['laba_rugi'] ?? 0, 0, ',', '.') }}</title>
                                </circle>
                            @endforeach

                            <!-- Gradients -->
                            <defs>
                                <linearGradient id="labaRugiGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#6B7280;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#EF4444;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>

                    <!-- X-axis labels -->
                    <div class="absolute bottom-0 left-16 right-2 flex justify-between text-xs text-gray-600 pb-1">
                        @foreach($chartData as $data)
                            <span class="font-semibold text-center">{{ $data['month'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-center gap-6 pt-2">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded"></div>
                    <span class="text-xs font-semibold text-gray-600">Laba</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-500 rounded"></div>
                    <span class="text-xs font-semibold text-gray-600">Rugi</span>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card app-card--soft space-y-5">
        <div>
            <p class="text-xs uppercase tracking-wide text-emerald-500 font-semibold">Rincian Bulanan</p>
            <h2 class="text-base font-semibold text-gray-900">Detail per bulan</h2>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-emerald-500 to-emerald-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Bulan/Tahun</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Pendapatan</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Pengeluaran</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Laba/Rugi</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Margin (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(($monthlyData ?? []) as $data)
                        <tr class="hover:bg-emerald-50 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::create(null, $data['bulan'], 1)->translatedFormat('M') }} {{ $data['tahun'] }}
                                </p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold text-green-600">Rp {{ number_format($data['pendapatan'], 0, ',', '.') }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold text-red-600">Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold {{ $data['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($data['laba_rugi'], 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold {{ $data['margin_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($data['margin_percentage'], 2) }}%
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada data</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-2">
            @forelse(($monthlyData ?? []) as $data)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::create(null, $data['bulan'], 1)->translatedFormat('F') }} {{ $data['tahun'] }}
                    </p>
                    <span class="text-xs font-semibold {{ $data['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($data['laba_rugi'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-600 font-semibold">Pendapatan:</span>
                        <p class="text-green-600 font-semibold">Rp {{ number_format($data['pendapatan'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 font-semibold">Pengeluaran:</span>
                        <p class="text-red-600 font-semibold">Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 font-semibold">Margin:</span>
                        <span class="text-sm font-semibold {{ $data['margin_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($data['margin_percentage'], 2) }}%
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="app-card text-center py-12">
                <div class="flex flex-col items-center">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada data</h3>
                    <p class="text-sm text-gray-500">Belum ada data yang ditemukan.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
