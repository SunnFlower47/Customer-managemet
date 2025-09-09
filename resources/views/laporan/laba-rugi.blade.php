@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Laporan Laba Rugi</h1>
        <p class="mt-1 text-sm text-gray-600">Laporan laba rugi perusahaan berdasarkan pendapatan dan pengeluaran</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('laporan.laba-rugi') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select name="tahun" id="tahun" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select name="bulan" id="bulan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-down text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $summary['laba_rugi'] >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-calculator {{ $summary['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laba/Rugi</p>
                    <p class="text-2xl font-bold {{ $summary['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($summary['laba_rugi'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Margin (%)</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['margin_percentage'], 2) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Pendapatan vs Pengeluaran Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pendapatan vs Pengeluaran</h3>
            <div class="h-64 relative overflow-hidden">
                <div class="grid grid-cols-12 gap-1 h-full items-end">
                    @foreach($chartData as $data)
                        <div class="flex flex-col items-center">
                            <div class="w-full relative group">
                                <!-- Pendapatan Bar -->
                                <div class="w-full bg-green-200 rounded-t-lg relative" style="height: {{ $data['pendapatan_height'] }}px; max-height: 200px;">
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                        Rp {{ number_format($data['pendapatan'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <!-- Pengeluaran Bar -->
                                <div class="w-full bg-red-200 rounded-b-lg relative" style="height: {{ $data['pengeluaran_height'] }}px; max-height: 200px;">
                                    <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                        Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-600 mt-2 text-center">{{ $data['month'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-center space-x-6 mt-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-200 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Pendapatan</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-200 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Pengeluaran</span>
                </div>
            </div>
        </div>

        <!-- Laba Rugi Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Laba/Rugi</h3>
            <div class="h-64 relative overflow-hidden">
                <div class="grid grid-cols-12 gap-1 h-full items-end">
                    @foreach($chartData as $data)
                        <div class="flex flex-col items-center">
                            <div class="w-full relative group">
                                @if($data['laba_rugi'] >= 0)
                                    <div class="w-full bg-green-200 rounded-t-lg relative" style="height: {{ $data['laba_rugi_height'] }}px;">
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                            +Rp {{ number_format($data['laba_rugi'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full bg-red-200 rounded-b-lg relative" style="height: {{ $data['laba_rugi_height'] }}px;">
                                        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                            -Rp {{ number_format(abs($data['laba_rugi']), 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <span class="text-xs text-gray-600 mt-2 text-center">{{ $data['month'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-center space-x-6 mt-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-200 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Laba</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-200 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Rugi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Rincian Bulanan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan/Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laba/Rugi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($monthlyData as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::create(null, $data['bulan'], 1)->translatedFormat('F') }} {{ $data['tahun'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600">Rp {{ number_format($data['pendapatan'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-red-600">Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium {{ $data['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($data['laba_rugi'], 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium {{ $data['margin_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($data['margin_percentage'], 2) }}%
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada data</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
