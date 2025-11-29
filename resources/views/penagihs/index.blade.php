@extends('layouts.app')

@section('title', 'Penagih - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-user-tie"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-purple-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Data Penagih</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola data penagih yang bertugas menagih pembayaran pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-purple-500"></i>{{ $penagihs->total() }} penagih
            </div>
            <a href="{{ route('penagihs.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah Penagih
            </a>
        </div>
    </div>

    <div class="app-card app-card--soft overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user-tie mr-2"></i>Nama Penagih
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-phone mr-2"></i>No HP
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Akun User
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
                    @forelse($penagihs as $penagih)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-sm flex items-center justify-center shadow relative">
                                    <div class="absolute -top-1 -right-1 h-3 w-3 bg-purple-500 rounded-full border-2 border-white"></div>
                                    {{ substr($penagih->nama, 0, 1) }}
                                </div>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $penagih->nama }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-xl truncate max-w-xs" title="{{ $penagih->email }}">
                                <i class="fas fa-envelope mr-1 text-gray-400"></i>{{ $penagih->email }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700 bg-blue-50 px-3 py-2 rounded-xl">
                                <i class="fas fa-phone mr-1 text-blue-600"></i>{{ $penagih->no_hp ?: '-' }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $penagih->aktif ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                <i class="fas fa-circle mr-1 text-[9px]"></i>{{ $penagih->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($penagih->user)
                                <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    <i class="fas fa-user mr-1"></i>{{ $penagih->user->name }}
                                </div>
                            @else
                                <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-100">
                                    <i class="fas fa-user-slash mr-1"></i>Tidak Ada
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                <i class="fas fa-users mr-1"></i>{{ $penagih->pelanggans->count() }} pelanggan
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-medium">
                            <div class="inline-flex flex-wrap justify-center gap-2">
                                <a href="{{ route('penagihs.show', $penagih) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                <a href="{{ route('penagihs.edit', $penagih) }}"
                                   class="inline-flex items-center px-3.5 py-2 text-[12px] bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition"
                                   title="Edit Data">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('penagihs.destroy', $penagih) }}" class="inline delete-form">
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
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-user-tie text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada penagih</h3>
                                <p class="text-gray-500 text-lg mb-6">Mulai dengan menambahkan penagih pertama Anda.</p>
                                <a href="{{ route('penagihs.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                                    <i class="fas fa-plus mr-2"></i>Tambah Penagih Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden space-y-3">
            @forelse($penagihs as $penagih)
            <div class="mobile-card bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-sm flex items-center justify-center relative">
                        <div class="absolute -top-1 -right-1 h-3 w-3 bg-purple-500 rounded-full border-2 border-white"></div>
                        {{ substr($penagih->nama, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-semibold text-gray-900 truncate">{{ $penagih->nama }}</p>
                        <p class="text-xs text-gray-500 truncate" title="{{ $penagih->email }}">{{ $penagih->email }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold {{ $penagih->aktif ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        {{ $penagih->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 my-3 text-[11px] text-gray-600">
                    <div class="bg-gray-50 px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-800 block mb-1">No HP</span>
                        <span class="text-gray-900 font-semibold">{{ $penagih->no_hp ?: '-' }}</span>
                    </div>
                    <div class="bg-gray-50 px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-800 block mb-1">Pelanggan</span>
                        <span class="text-gray-900 font-semibold">{{ $penagih->pelanggans->count() }} pelanggan</span>
                    </div>
                </div>
                @if($penagih->user)
                <div class="mb-3 bg-blue-50 px-3 py-2 rounded-xl border border-blue-100">
                    <span class="text-[11px] font-semibold text-gray-800 block mb-1">Akun User</span>
                    <span class="text-xs text-blue-700 font-semibold flex items-center gap-1">
                        <i class="fas fa-user"></i>{{ $penagih->user->name }}
                    </span>
                </div>
                @endif
                <div class="grid grid-cols-3 gap-2 text-[11px] font-semibold">
                    <a href="{{ route('penagihs.show', $penagih) }}" class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mb-1"></i>Detail
                    </a>
                    <a href="{{ route('penagihs.edit', $penagih) }}" class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-edit mb-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('penagihs.destroy', $penagih) }}" class="delete-form">
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
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-user-tie text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada penagih</h3>
                <p class="text-gray-500 text-lg mb-6">Mulai dengan menambahkan penagih pertama Anda.</p>
                <a href="{{ route('penagihs.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg hover:scale-105 transition-all duration-200 font-bold">
                    <i class="fas fa-plus mr-2"></i>Tambah Penagih Pertama
                </a>
            </div>
            @endforelse
        </div>
    </div>

    @if($penagihs->hasPages())
    <div class="app-card">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs sm:text-sm text-gray-700">
                Menampilkan
                <span class="font-medium">{{ $penagihs->firstItem() }}</span>
                sampai
                <span class="font-medium">{{ $penagihs->lastItem() }}</span>
                dari
                <span class="font-medium">{{ $penagihs->total() }}</span>
                penagih
            </div>
            <div class="flex space-x-2">
                {{ $penagihs->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            let penagihName = '';
            const tableRow = form.closest('tr');
            if (tableRow) {
                penagihName = tableRow.querySelector('td:first-child').textContent.trim();
            } else {
                const cardContainer = form.closest('.mobile-card');
                if (cardContainer) {
                    const nameElement = cardContainer.querySelector('.font-semibold');
                    if (nameElement) {
                        penagihName = nameElement.textContent.trim();
                    }
                }
            }
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
                    form.submit();
                }
            });
        });
    });

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
