@extends('layouts.app')

@section('title', 'Data Pengeluaran - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-receipt"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Data Pengeluaran</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola pengeluaran operasional WiFi Billing</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-red-500"></i>{{ $pengeluarans->total() }} pengeluaran
            </div>
            <a href="{{ route('pengeluarans.export', request()->query()) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-file-pdf mr-2 text-xs sm:text-sm"></i>Export PDF
            </a>
            <a href="{{ route('pengeluarans.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah Pengeluaran
            </a>
        </div>
    </div>

    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-search mr-2 text-red-500"></i>Pencarian & Filter
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-red-500"></i>
                Gunakan filter untuk menemukan pengeluaran dengan mudah
            </p>
        </div>
        <form method="GET" action="{{ route('pengeluarans.index') }}" class="space-y-6">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-bold text-gray-700 mb-3">
                    <i class="fas fa-search mr-2 text-red-600"></i>Cari Pengeluaran
                </label>
                <div class="relative">
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                           placeholder="Cari berdasarkan nama, deskripsi, atau kategori...">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="kategori" class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-tags mr-2 text-red-600"></i>Kategori
                    </label>
                    <select name="kategori"
                            id="kategori"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-info-circle mr-2 text-red-600"></i>Status
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="metode_pembayaran" class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-credit-card mr-2 text-red-600"></i>Metode
                    </label>
                    <select name="metode_pembayaran"
                            id="metode_pembayaran"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Metode</option>
                        @foreach($metodeOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('metode_pembayaran') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bulan" class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-calendar mr-2 text-red-600"></i>Bulan
                    </label>
                    <select name="bulan"
                            id="bulan"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="tahun" class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-calendar mr-2 text-red-600"></i>Tahun
                    </label>
                    <select name="tahun"
                            id="tahun"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        <option value="">Semua Tahun</option>
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 col-span-1 md:col-span-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-filter mr-2"></i>Filter Data
                </button>
                <a href="{{ route('pengeluarans.index') }}"
                   class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl hover:bg-gray-50 transition text-sm font-semibold text-center">
                    <i class="fas fa-refresh mr-2"></i>Reset Filter
                </a>
            </div>
        </div>
        </form>
    </div>

    <div class="app-card app-card--soft overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-receipt mr-2"></i>Pengeluaran
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tags mr-2"></i>Kategori
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-money-bill mr-2"></i>Jumlah
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Tanggal
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-credit-card mr-2"></i>Metode
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pengeluarans as $pengeluaran)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow text-white relative">
                                    <div class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full border-2 border-white"></div>
                                    <i class="fas fa-receipt text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $pengeluaran->nama_pengeluaran }}</p>
                                    @if($pengeluaran->deskripsi)
                                        <p class="text-xs text-gray-500 truncate max-w-xs" title="{{ $pengeluaran->deskripsi }}">{{ $pengeluaran->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fas fa-tag mr-1 text-[9px]"></i>{{ $kategoriOptions[$pengeluaran->kategori] ?? $pengeluaran->kategori }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-red-600 bg-red-50 px-3 py-2 rounded-xl">
                                <i class="fas fa-minus mr-1"></i>{{ $pengeluaran->formatted_jumlah }}
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700 bg-gray-50 px-3 py-2 rounded-xl">
                                <i class="fas fa-calendar mr-1 text-gray-400"></i>{{ $pengeluaran->tanggal_pengeluaran->format('d M Y') }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $pengeluaran->metode_badge_class }}">
                                <i class="fas fa-credit-card mr-1 text-[9px]"></i>{{ $metodeOptions[$pengeluaran->metode_pembayaran] ?? $pengeluaran->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $pengeluaran->status_badge_class }}">
                                <i class="fas fa-circle mr-1 text-[9px]"></i>{{ $statusOptions[$pengeluaran->status] ?? $pengeluaran->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-medium">
                            <div class="inline-flex flex-wrap justify-center gap-2">
                                <a href="{{ route('pengeluarans.show', $pengeluaran) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                <a href="{{ route('pengeluarans.edit', $pengeluaran) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:shadow-lg transition"
                                   title="Edit">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                <form action="{{ route('pengeluarans.destroy', $pengeluaran) }}"
                                      method="POST"
                                      class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition"
                                            title="Hapus">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-receipt text-white text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada pengeluaran</h3>
                                <p class="text-gray-500 mb-6">Belum ada data pengeluaran yang tercatat.</p>
                                <a href="{{ route('pengeluarans.create') }}"
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                                    <i class="fas fa-plus mr-2"></i>Tambah Pengeluaran Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-3">
            @forelse($pengeluarans as $pengeluaran)
            <div class="mobile-card bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 text-white flex items-center justify-center shadow">
                        <i class="fas fa-receipt text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-semibold text-gray-900 truncate">{{ $pengeluaran->nama_pengeluaran }}</p>
                        <p class="text-lg font-bold text-red-600">{{ $pengeluaran->formatted_jumlah }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold {{ $pengeluaran->status_badge_class }}">
                        {{ $statusOptions[$pengeluaran->status] ?? $pengeluaran->status }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 my-3 text-[11px] text-gray-600">
                    <div class="bg-gray-50 px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-800 block mb-1">Kategori</span>
                        <span class="text-gray-900 font-semibold">{{ $kategoriOptions[$pengeluaran->kategori] ?? $pengeluaran->kategori }}</span>
                    </div>
                    <div class="bg-gray-50 px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-800 block mb-1">Tanggal</span>
                        <span class="text-gray-900 font-semibold">{{ $pengeluaran->tanggal_pengeluaran->format('d M Y') }}</span>
                    </div>
                </div>
                @if($pengeluaran->deskripsi)
                <div class="mb-3 bg-gray-50 px-3 py-2 rounded-xl">
                    <p class="text-xs text-gray-600 line-clamp-2">{{ $pengeluaran->deskripsi }}</p>
                </div>
                @endif
                <div class="grid grid-cols-3 gap-2 text-[11px] font-semibold">
                    <a href="{{ route('pengeluarans.show', $pengeluaran) }}" class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mb-1"></i>Detail
                    </a>
                    <a href="{{ route('pengeluarans.edit', $pengeluaran) }}" class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-edit mb-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('pengeluarans.destroy', $pengeluaran) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-md transition">
                            <i class="fas fa-trash mb-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-receipt text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada pengeluaran</h3>
                <p class="text-gray-500 mb-6">Belum ada data pengeluaran yang tercatat.</p>
                <a href="{{ route('pengeluarans.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 font-bold">
                    <i class="fas fa-plus mr-2"></i>Tambah Pengeluaran Pertama
                </a>
            </div>
            @endforelse
        </div>

    </div>

    @if($pengeluarans->hasPages())
    <div class="app-card">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs sm:text-sm text-gray-700">
                Menampilkan
                <span class="font-medium">{{ $pengeluarans->firstItem() }}</span>
                sampai
                <span class="font-medium">{{ $pengeluarans->lastItem() }}</span>
                dari
                <span class="font-medium">{{ $pengeluarans->total() }}</span>
                pengeluaran
            </div>
            <div class="flex space-x-2">
                {{ $pengeluarans->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
    @endif
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
            let pengeluaranDesc = '';
            const tableRow = form.closest('tr');
            if (tableRow) {
                pengeluaranDesc = tableRow.querySelector('td:first-child').textContent.trim();
            } else {
                const cardContainer = form.closest('.mobile-card');
                if (cardContainer) {
                    const nameElement = cardContainer.querySelector('.font-semibold');
                    if (nameElement) {
                        pengeluaranDesc = nameElement.textContent.trim();
                    }
                }
            }

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
