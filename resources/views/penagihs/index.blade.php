@extends('layouts.app')

@section('title', 'Penagih - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Data Penagih</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola data penagih yang bertugas menagih pembayaran pelanggan.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('penagihs.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Penagih
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Nama Penagih
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    No HP
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Akun User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Pelanggan
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($penagihs as $penagih)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $penagih->nama }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 break-all max-w-xs">{{ $penagih->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $penagih->no_hp ?: '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penagih->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $penagih->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($penagih->user)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $penagih->user->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Tidak Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $penagih->pelanggans->count() }} pelanggan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        <!-- Tombol Detail -->
                                        <a href="{{ route('penagihs.show', $penagih) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition duration-150"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>

                                        <!-- Tombol Edit -->
                                        <a href="{{ route('penagihs.edit', $penagih) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition duration-150"
                                           title="Edit Data">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form method="POST" action="{{ route('penagihs.destroy', $penagih) }}" class="inline delete-form">
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
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada penagih yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            @forelse($penagihs as $penagih)
            <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-blue-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="text-lg font-medium text-gray-900 truncate">{{ $penagih->nama }}</div>
                                <div class="text-sm text-gray-500 break-all">{{ $penagih->email }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                            <div>
                                <span class="font-medium">No HP:</span><br>
                                <span>{{ $penagih->no_hp }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span><br>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($penagih->status === 'aktif') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($penagih->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                {{ $penagih->pelanggans_count }} pelanggan
                            </span>
                            @if($penagih->user)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-user mr-1"></i>Ada Akun
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-user-slash mr-1"></i>Belum Ada Akun
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 ml-4">
                        <a href="{{ route('penagihs.show', $penagih) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('penagihs.edit', $penagih) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('penagihs.destroy', $penagih) }}" class="delete-form">
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
                <i class="fas fa-user-tie text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada penagih</h3>
                <p class="text-gray-500">Mulai dengan menambahkan penagih pertama Anda.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($penagihs->hasPages())
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
        {{ $penagihs->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Penagihs page loaded, initializing SweetAlert...');

    // Handle delete confirmation with SweetAlert
    const deleteForms = document.querySelectorAll('.delete-form');
    console.log('Found delete forms:', deleteForms.length);

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Delete form submitted');

            const form = this;
            let penagihName = '';

            // Try to get penagih name from desktop table (tr)
            const tableRow = form.closest('tr');
            if (tableRow) {
                penagihName = tableRow.querySelector('td:first-child').textContent.trim();
            } else {
                // Try to get penagih name from mobile card
                const cardContainer = form.closest('.border-b');
                if (cardContainer) {
                    const nameElement = cardContainer.querySelector('h3, .font-medium, .text-lg');
                    if (nameElement) {
                        penagihName = nameElement.textContent.trim();
                    }
                }
            }

            console.log('Penagih to delete:', penagihName);

            Swal.fire({
                title: 'Hapus Penagih?',
                text: `Apakah Anda yakin ingin menghapus penagih "${penagihName}"?`,
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
