@extends('layouts.app')

@section('title', 'Paket - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-box mr-3 text-blue-600"></i>Paket Internet
            </h1>
            <p class="mt-2 text-sm text-gray-700">Kelola paket internet yang tersedia untuk pelanggan.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('pakets.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <i class="fas fa-plus mr-2"></i>Tambah Paket
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-tag mr-2 text-gray-400"></i>Nama Paket
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>Harga
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-align-left mr-2 text-gray-400"></i>Deskripsi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-users mr-2 text-gray-400"></i>Pelanggan
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-2 text-gray-400"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pakets as $paket)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-sm">{{ substr($paket->nama_paket, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $paket->nama_paket }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($paket->deskripsi, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paket->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $paket->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $paket->pelanggans->count() }} pelanggan
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <!-- Tombol Detail -->
                                <a href="{{ route('pakets.show', $paket) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition duration-150"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>

                                <!-- Tombol Edit -->
                                <a href="{{ route('pakets.edit', $paket) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition duration-150"
                                   title="Edit Data">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>

                                <!-- Tombol Hapus -->
                                <form method="POST" action="{{ route('pakets.destroy', $paket) }}" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition duration-150"
                                            title="Hapus Data">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-box text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada paket</h3>
                                <p class="text-gray-500">Belum ada data paket yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            @forelse($pakets as $paket)
            <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-box text-blue-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-lg font-medium text-gray-900">{{ $paket->nama_paket }}</div>
                                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm text-gray-600">{{ $paket->deskripsi }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($paket->status === 'aktif') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($paket->status) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ $paket->pelanggans_count }} pelanggan
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 ml-4">
                        <a href="{{ route('pakets.show', $paket) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('pakets.edit', $paket) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('pakets.destroy', $paket) }}" class="delete-form">
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
                <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada paket</h3>
                <p class="text-gray-500">Mulai dengan menambahkan paket pertama Anda.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($pakets->hasPages())
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
        {{ $pakets->appends(request()->query())->links('vendor.pagination.tailwind') }}
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
                paketName = tableRow.querySelector('td:first-child').textContent.trim();
            } else {
                // Try to get paket name from mobile card
                const cardContainer = form.closest('.border-b');
                if (cardContainer) {
                    const nameElement = cardContainer.querySelector('h3, .font-medium, .text-lg');
                    if (nameElement) {
                        paketName = nameElement.textContent.trim();
                    }
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
