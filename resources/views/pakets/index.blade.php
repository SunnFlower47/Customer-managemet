@extends('layouts.app')

@section('title', 'Paket - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-box"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Paket Internet</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Atur paket dan harga yang ditawarkan kepada pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>{{ $pakets->total() }} paket aktif
            </div>
            <a href="{{ route('pakets.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah Paket
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
        <!-- PPN Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total PPN</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs font-bold text-blue-500">Rp</span>
                        <h3 class="text-xl font-bold text-slate-900">{{ number_format($stats['total_ppn'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- BHP Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-broadcast-tower text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total BHP</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs font-bold text-indigo-500">Rp</span>
                        <h3 class="text-xl font-bold text-slate-900">{{ number_format($stats['total_bhp'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- USO Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                    <i class="fas fa-globe text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total USO</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs font-bold text-purple-500">Rp</span>
                        <h3 class="text-xl font-bold text-slate-900">{{ number_format($stats['total_uso'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADM Card -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total ADM</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs font-bold text-orange-500">Rp</span>
                        <h3 class="text-xl font-bold text-slate-900">{{ number_format($stats['total_adm'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grand Total Card -->
        <div class="bg-blue-600 rounded-2xl p-5 shadow-lg shadow-blue-100 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-blue-100 uppercase tracking-wider mb-1">Total Semua Pajak</p>
                    <div class="flex items-baseline gap-1 text-white">
                        <span class="text-xs font-bold opacity-70">Rp</span>
                        <h3 class="text-xl font-bold">{{ number_format($stats['grand_total_pajak'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card app-card--soft overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Nama Paket
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-dollar-sign mr-2"></i>Harga
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-align-left mr-2"></i>Deskripsi
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-users mr-2"></i>Pelanggan
                        </th>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pakets as $paket)
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-sm flex items-center justify-center shadow">
                                    {{ substr($paket->nama_paket, 0, 1) }}
                                </div>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $paket->nama_paket }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-2.5 rounded-2xl border border-green-100">
                                <p class="text-base font-bold text-green-900">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-xl line-clamp-2">{{ Str::limit($paket->deskripsi, 70) }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $paket->aktif ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                <i class="fas fa-circle mr-1 text-[9px]"></i>{{ $paket->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                <i class="fas fa-users mr-1"></i>{{ $paket->pelanggans->count() }} pelanggan
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-medium">
                            <div class="inline-flex flex-wrap justify-center gap-2">
                                <a href="{{ route('pakets.show', $paket) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                <a href="{{ route('pakets.edit', $paket) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition"
                                   title="Edit Data">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('pakets.destroy', $paket) }}" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition"
                                            title="Hapus Data">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-box text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada paket</h3>
                                <p class="text-gray-500 text-lg mb-6">Mulai dengan menambahkan paket pertama Anda.</p>
                                <a href="{{ route('pakets.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                                    <i class="fas fa-plus mr-2"></i>Tambah Paket Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-3">
            @forelse($pakets as $paket)
            <div class="mobile-card bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-sm flex items-center justify-center">
                        {{ substr($paket->nama_paket, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-base font-semibold text-gray-900 truncate">{{ $paket->nama_paket }}</p>
                        <p class="text-sm font-bold text-green-700">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold {{ $paket->aktif ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        {{ $paket->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    {{ Str::limit($paket->deskripsi, 110) ?: 'Tidak ada deskripsi' }}
                </p>
                <div class="grid grid-cols-2 gap-2 my-3 text-[11px] text-gray-600">
                    <div class="bg-gray-50 px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-800 block mb-1">Pelanggan</span>
                        <span class="text-gray-900 font-semibold">{{ $paket->pelanggans->count() }} pelanggan</span>
                    </div>
                    <div class="bg-gray-50 px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-800 block mb-1">Terakhir dibuat</span>
                        <span class="text-gray-900 font-semibold">{{ $paket->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[11px] font-semibold">
                    <a href="{{ route('pakets.show', $paket) }}" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mr-1.5"></i>Detail
                    </a>
                    <a href="{{ route('pakets.edit', $paket) }}" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-edit mr-1.5"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('pakets.destroy', $paket) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-md transition w-full">
                            <i class="fas fa-trash mr-1.5"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-box text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada paket</h3>
                <p class="text-gray-500 text-lg mb-6">Mulai dengan menambahkan paket pertama Anda.</p>
                <a href="{{ route('pakets.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                    <i class="fas fa-plus mr-2"></i>Tambah Paket Pertama
                </a>
            </div>
            @endforelse
        </div>
    </div>

    @if($pakets->hasPages())
    <div class="app-card">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs sm:text-sm text-gray-600">
            <span>Menampilkan <strong>{{ $pakets->firstItem() }}</strong> - <strong>{{ $pakets->lastItem() }}</strong> dari <strong>{{ $pakets->total() }}</strong> paket</span>
            <div class="flex justify-center sm:justify-end">
                {{ $pakets->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pakets page loaded, initializing SweetAlert...');

    // Handle delete confirmation with SweetAlert
    const deleteForms = document.querySelectorAll('.delete-form');
    console.log('Found delete forms:', deleteForms.length);

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Delete form submitted');

            const form = this;
            let paketName = '';

            // Try to get paket name from desktop table (tr)
            const tableRow = form.closest('tr');
                if (tableRow) {
                    const nameCell = tableRow.querySelector('td:first-child .text-sm');
                    paketName = nameCell ? nameCell.textContent.trim() : '';
                } else {
                    const cardContainer = form.closest('.mobile-card');
                    if (cardContainer) {
                        const nameElement = cardContainer.querySelector('.text-base.font-semibold') || cardContainer.querySelector('.text-lg');
                        paketName = nameElement ? nameElement.textContent.trim() : '';
                    }
                }

            console.log('Paket to delete:', paketName);

            Swal.fire({
                title: 'Hapus Paket?',
                text: `Apakah Anda yakin ingin menghapus paket "${paketName}"?`,
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

});
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
@endpush
@endsection
