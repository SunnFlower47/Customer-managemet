@extends('layouts.app')

@section('title', 'Data Pengeluaran - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Pengeluaran</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola pengeluaran operasional WiFi Billing</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('pengeluarans.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="hidden sm:inline">Tambah Pengeluaran</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
                <a href="{{ route('pengeluarans.export', request()->query()) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>
                    <span class="hidden sm:inline">Export PDF</span>
                    <span class="sm:hidden">PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('pengeluarans.index') }}" class="space-y-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1 text-gray-400"></i>Search
                </label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                       placeholder="Cari berdasarkan nama, deskripsi, atau kategori...">
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-1 text-gray-400"></i>Kategori
                    </label>
                    <select name="kategori"
                            id="kategori"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-1 text-gray-400"></i>Status
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-1 text-gray-400"></i>Metode
                    </label>
                    <select name="metode_pembayaran"
                            id="metode_pembayaran"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Metode</option>
                        @foreach($metodeOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('metode_pembayaran') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1 text-gray-400"></i>Bulan
                    </label>
                    <select name="bulan"
                            id="bulan"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1 text-gray-400"></i>Tahun
                    </label>
                    <select name="tahun"
                            id="tahun"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Tahun</option>
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-semibold">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-receipt mr-2 text-blue-600"></i>Daftar Pengeluaran
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $pengeluarans->total() }} total)</span>
            </h3>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-receipt mr-1"></i>Pengeluaran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-tags mr-1"></i>Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-money-bill mr-1"></i>Jumlah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-1"></i>Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-credit-card mr-1"></i>Metode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-1"></i>Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengeluarans as $pengeluaran)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <i class="fas fa-receipt text-red-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $pengeluaran->nama_pengeluaran }}</div>
                                    @if($pengeluaran->deskripsi)
                                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $pengeluaran->deskripsi }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $kategoriOptions[$pengeluaran->kategori] ?? $pengeluaran->kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-red-600">- {{ $pengeluaran->formatted_jumlah }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $pengeluaran->tanggal_pengeluaran->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pengeluaran->metode_badge_class }}">
                                {{ $metodeOptions[$pengeluaran->metode_pembayaran] ?? $pengeluaran->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pengeluaran->status_badge_class }}">
                                {{ $statusOptions[$pengeluaran->status] ?? $pengeluaran->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('pengeluarans.show', $pengeluaran) }}"
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pengeluarans.edit', $pengeluaran) }}"
                                   class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pengeluarans.destroy', $pengeluaran) }}"
                                      method="POST"
                                      class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengeluaran</h3>
                                <p class="text-gray-500">Belum ada data pengeluaran yang tercatat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            @forelse($pengeluarans as $pengeluaran)
            <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-lg bg-red-50 flex items-center justify-center">
                                    <i class="fas fa-money-bill-alt text-red-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-lg font-medium text-gray-900">{{ $pengeluaran->nama_pengeluaran }}</div>
                                <div class="text-2xl font-bold text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                            <div>
                                <span class="font-medium">Kategori:</span><br>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $pengeluaran->kategori }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Tanggal:</span><br>
                                <span>{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        @if($pengeluaran->deskripsi)
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">{{ Str::limit($pengeluaran->deskripsi, 100) }}</p>
                        </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($pengeluaran->status === 'terkonfirmasi') bg-green-100 text-green-800
                                @elseif($pengeluaran->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($pengeluaran->status) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $pengeluaran->metode_pembayaran }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 ml-4">
                        <a href="{{ route('pengeluarans.show', $pengeluaran) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('pengeluarans.edit', $pengeluaran) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('pengeluarans.destroy', $pengeluaran) }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-money-bill-alt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengeluaran</h3>
                <p class="text-gray-500">Belum ada data pengeluaran yang tercatat.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($pengeluarans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pengeluarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pengeluarans page loaded, initializing SweetAlert...');

    // Handle delete confirmation with SweetAlert
    const deleteForms = document.querySelectorAll('.delete-form');
    console.log('Found delete forms:', deleteForms.length);

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Delete form submitted');

            const form = this;
            const pengeluaranDesc = form.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            console.log('Pengeluaran to delete:', pengeluaranDesc);

            Swal.fire({
                title: 'Hapus Pengeluaran?',
                text: `Apakah Anda yakin ingin menghapus pengeluaran "${pengeluaranDesc}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('User confirmed deletion');
                    form.submit();
                } else {
                    console.log('User cancelled deletion');
                }
            });
        });
    });

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
});
</script>
@endpush
@endsection
