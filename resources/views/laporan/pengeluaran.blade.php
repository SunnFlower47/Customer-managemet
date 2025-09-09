@extends('layouts.app')

@section('title', 'Laporan Pengeluaran')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Laporan Pengeluaran</h1>
        <p class="mt-1 text-sm text-gray-600">Laporan pengeluaran operasional perusahaan</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('laporan.pengeluaran') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Jumlah Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['total_transaksi'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calculator text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata per Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Pengeluaran Bulanan</h3>
        <div class="h-64 flex items-end justify-between space-x-2 relative overflow-hidden">
            @foreach($chartData as $data)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-full bg-red-200 rounded-t-lg relative group" style="height: {{ $data['height'] }}px; max-height: 200px;">
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
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Detail Pengeluaran</h3>
            @if((auth()->user()?->role ?? 'guest') === 'admin')
                <a href="{{ route('pengeluarans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <i class="fas fa-plus mr-2"></i>Tambah Pengeluaran
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengeluarans as $pengeluaran)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $pengeluaran->deskripsi }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $pengeluaran->user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('pengeluarans.show', $pengeluaran) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if((auth()->user()?->role ?? 'guest') === 'admin')
                                        <a href="{{ route('pengeluarans.edit', $pengeluaran) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('pengeluarans.destroy', $pengeluaran) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada data pengeluaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengeluarans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pengeluarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
