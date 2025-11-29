@extends('layouts.app')

@section('title', 'ODP - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-map-marker-alt"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-purple-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">ODP Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola Optical Distribution Point (ODP) dan lokasinya</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('mapping.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition">
                <i class="fas fa-map mr-2 text-xs sm:text-sm"></i>Lihat Peta
            </a>
            @can('edit-pelanggan')
            <a href="{{ route('odps.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah ODP
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="app-card app-card--soft">
        <form method="GET" action="{{ route('odps.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Kode, nama, atau alamat ODP"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Status
                </label>
                <select name="status"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('odps.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Desktop Table -->
    <div class="app-card app-card--soft overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-barcode mr-2"></i>Kode ODP
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Nama
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-map-marker-alt mr-2"></i>Koordinat
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-plug mr-2"></i>Kapasitas
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-users mr-2"></i>Pelanggan
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($odps as $odp)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white font-bold text-sm flex items-center justify-center shadow">
                                    {{ substr($odp->kode_odp, 0, 2) }}
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ $odp->kode_odp }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $odp->nama }}</p>
                            @if($odp->alamat)
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ Str::limit($odp->alamat, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-600">
                                <div><i class="fas fa-latitude mr-1"></i>{{ number_format($odp->latitude, 6) }}</div>
                                <div><i class="fas fa-longitude mr-1"></i>{{ number_format($odp->longitude, 6) }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                <i class="fas fa-plug mr-1"></i>{{ $odp->port_terpakai }}/{{ $odp->kapasitas }}
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                <i class="fas fa-users mr-1"></i>{{ $odp->pelanggans_count }} pelanggan
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $odp->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                <i class="fas fa-circle mr-1 text-[9px]"></i>{{ ucfirst($odp->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <div class="inline-actions">
                                <a href="{{ route('odps.show', $odp) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit-pelanggan')
                                <a href="{{ route('odps.edit', $odp) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('odps.destroy', $odp) }}" method="POST" class="inline delete-form" data-message="Yakin ingin menghapus ODP ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
                                <p class="text-sm font-semibold">Tidak ada ODP ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-3.5">
            @forelse($odps as $odp)
            <div class="mobile-card hover:shadow-xl transition-all duration-200">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-semibold text-base shadow-md flex-shrink-0">
                        {{ substr($odp->kode_odp, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $odp->kode_odp }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $odp->nama }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0
                                @if($odp->status === 'aktif') bg-emerald-50 text-emerald-600 border border-emerald-200
                                @else bg-rose-50 text-rose-600 border border-rose-200 @endif">
                                <i class="fas fa-circle mr-1 text-[7px]"></i>{{ ucfirst($odp->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-2.5">
                        <p class="text-[10px] font-semibold text-purple-600 mb-1">Kapasitas Port</p>
                        <p class="text-xs font-semibold text-purple-800">{{ $odp->port_terpakai }}/{{ $odp->kapasitas }}</p>
                        @php
                            $usagePercent = $odp->kapasitas > 0 ? ($odp->port_terpakai / $odp->kapasitas) * 100 : 0;
                        @endphp
                        <div class="mt-1 w-full bg-purple-200 rounded-full h-1.5">
                            <div class="bg-purple-600 h-1.5 rounded-full {{ $usagePercent >= 80 ? 'bg-red-500' : ($usagePercent >= 50 ? 'bg-yellow-500' : '') }}" style="width: {{ $usagePercent }}%"></div>
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-100 rounded-lg p-2.5">
                        <p class="text-[10px] font-semibold text-green-600 mb-1">Pelanggan</p>
                        <p class="text-xs font-semibold text-green-800">{{ $odp->pelanggans_count }} pelanggan aktif</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-2.5">
                        <p class="text-[10px] font-semibold text-blue-600 mb-1">Koordinat</p>
                        <p class="font-mono text-[10px] text-blue-900 break-all">{{ number_format($odp->latitude, 6) }}</p>
                        <p class="font-mono text-[10px] text-blue-900 break-all">{{ number_format($odp->longitude, 6) }}</p>
                    </div>
                    @if($odp->alamat)
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5">
                        <p class="text-[10px] font-semibold text-gray-500 mb-1">Alamat</p>
                        <p class="text-xs text-gray-900 line-clamp-2">{{ $odp->alamat }}</p>
                    </div>
                    @else
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5">
                        <p class="text-[10px] font-semibold text-gray-500 mb-1">Alamat</p>
                        <p class="text-xs text-gray-400 italic">Tidak ada alamat</p>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('odps.show', $odp) }}"
                       class="inline-flex flex-col items-center justify-center px-3.5 py-2 text-[11px] font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 text-center">
                        <i class="fas fa-eye mb-1 text-xs"></i>Detail
                    </a>
                    @can('edit-pelanggan')
                    <a href="{{ route('odps.edit', $odp) }}"
                       class="inline-flex flex-col items-center justify-center px-3.5 py-2 text-[11px] font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 text-center">
                        <i class="fas fa-edit mb-1 text-xs"></i>Edit
                    </a>
                    <form action="{{ route('odps.destroy', $odp) }}" method="POST" class="delete-form w-full" data-message="Yakin ingin menghapus ODP ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex flex-col items-center justify-center px-3.5 py-2 text-[11px] font-semibold bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 w-full text-center">
                            <i class="fas fa-trash mb-1 text-xs"></i>Hapus
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-map-marker-alt text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada ODP</h3>
                <p class="text-sm text-gray-500 mb-6">Belum ada ODP yang terdaftar dalam sistem</p>
                @can('edit-pelanggan')
                <a href="{{ route('odps.create') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah ODP Pertama
                </a>
                @endcan
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($odps->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $odps->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (this.dataset.confirmed === 'true') {
                    this.dataset.confirmed = 'false';
                    return true;
                }

                event.preventDefault();

                const message = this.dataset.message || 'Yakin ingin menghapus data ini?';
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#9CA3AF',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.dataset.confirmed = 'true';
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endpush

