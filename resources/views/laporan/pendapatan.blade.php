@extends('layouts.app')

@section('title', 'Laporan Pendapatan - {{ \App\Models\CompanyProfile::first()->initials ?? "BCM" }} WiFi Billing')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Laporan Pendapatan</h1>
        <p class="mt-1 text-sm text-gray-600">Laporan pendapatan dari pembayaran pelanggan</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('laporan.pendapatan') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pembayaran Lunas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['pembayaran_lunas'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pembayaran Belum Lunas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['pembayaran_belum_lunas'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pelanggan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['total_pelanggan'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Pendapatan Bulanan</h3>
        <div class="h-64 flex items-end justify-between space-x-2 relative overflow-hidden">
            @foreach($chartData as $data)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-full bg-blue-200 rounded-t-lg relative group" style="height: {{ $data['height'] }}px; max-height: 200px;">
                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            Rp {{ number_format($data['amount'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-xs text-gray-600 mt-2">{{ $data['month'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Detail Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Pembayaran</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penagih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan/Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayarans as $pembayaran)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $pembayaran->pelanggan->nama }}</div>
                                <div class="text-sm text-gray-500 font-mono">{{ \Illuminate\Support\Str::limit($pembayaran->pelanggan->pppoe, 20) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($pembayaran->pelanggan->penagih)
                                        {{ $pembayaran->pelanggan->penagih->nama }}
                                    @else
                                        <span class="text-gray-400 italic">Belum ada penagih</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->translatedFormat('F') }} {{ $pembayaran->tahun_tagihan }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pembayaran->status == 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($pembayaran->tanggal_bayar)
                                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada data pembayaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pembayarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>

    <!-- Mobile Cards -->
    <div class="lg:hidden">
        @forelse($pembayarans as $pembayaran)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
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
                            <div class="text-xs text-gray-500 font-mono break-all">{{ \Illuminate\Support\Str::limit($pembayaran->pelanggan->pppoe, 30) }}</div>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    @if($pembayaran->status == 'lunas')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Lunas
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Belum Lunas
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs text-gray-600 mb-3">
                <div>
                    <span class="font-medium">Penagih:</span><br>
                    <span>{{ $pembayaran->pelanggan->penagih ? $pembayaran->pelanggan->penagih->nama : 'Belum ada penagih' }}</span>
                </div>
                <div>
                    <span class="font-medium">Periode:</span><br>
                    <span>{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->translatedFormat('M') }} {{ $pembayaran->tahun_tagihan }}</span>
                </div>
                <div>
                    <span class="font-medium">Jumlah:</span><br>
                    <span class="font-semibold text-gray-900">Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="font-medium">Tanggal Bayar:</span><br>
                    <span>
                        @if($pembayaran->tanggal_bayar)
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-chart-line text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data</h3>
                <p class="text-gray-500">Belum ada data pembayaran yang ditemukan.</p>
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
