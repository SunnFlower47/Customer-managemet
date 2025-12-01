@extends('layouts.app')

@section('title', 'ODC - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">ODC Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola Optical Distribution Cabinet (ODC) dan relasi ke ODP</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            @can('manage-odp')
            <a href="{{ route('odcs.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah ODC
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="app-card app-card--soft">
        <form method="GET" action="{{ route('odcs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Kode, nama, atau alamat ODC"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Status
                </label>
                <select name="status"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="penuh" {{ request('status') === 'penuh' ? 'selected' : '' }}>Penuh</option>
                    <option value="rusak" {{ request('status') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('odcs.index') }}"
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
                <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-barcode mr-2"></i>Kode ODC
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Nama
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-plug mr-2"></i>Port Terpakai
                        </th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-project-diagram mr-2"></i>Jumlah ODP
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
                    @forelse($odcs as $odc)
                    <tr class="hover:bg-indigo-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white font-bold text-sm flex items-center justify-center shadow">
                                    {{ substr($odc->kode_odc, 0, 2) }}
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ $odc->kode_odc }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $odc->nama }}</p>
                            @if($odc->alamat)
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ Str::limit($odc->alamat, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @php
                                $usedPorts = $odc->direct_odps_count ?? 0;
                                $capacity = max(0, $odc->kapasitas_port);
                                $usagePercent = $capacity > 0 ? min(100, ($usedPorts / $capacity) * 100) : 0;
                            @endphp
                            <div class="space-y-1">
                                <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i class="fas fa-plug mr-1"></i>{{ $usedPorts }}/{{ $capacity }} port
                                </div>
                                <div class="w-32 bg-indigo-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full @if($usagePercent >= 100) bg-red-500 @elseif($usagePercent >= 80) bg-amber-500 @else bg-indigo-500 @endif" style="width: {{ $usagePercent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $odc->total_odps_count ?? $odc->odps->count() }} ODP
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @php
                                $usedPorts = $odc->direct_odps_count ?? 0;
                                $capacity = max(0, $odc->kapasitas_port);
                                $computedStatus = $capacity > 0 && $usedPorts >= $capacity ? 'penuh' : $odc->status;
                                $statusClass = match($computedStatus) {
                                    'aktif' => 'bg-green-100 text-green-800 border border-green-200',
                                    'penuh' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                    'rusak' => 'bg-red-100 text-red-800 border border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border border-gray-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $statusClass }}">
                                <i class="fas fa-circle mr-1 text-[9px]"></i>{{ ucfirst($computedStatus) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <div class="inline-actions">
                                <a href="{{ route('odcs.show', $odc) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('manage-odp')
                                <a href="{{ route('odcs.edit', $odc) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('odcs.destroy', $odc) }}" method="POST" class="inline delete-form" data-message="Yakin ingin menghapus ODC ini?">
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
                        <td colspan="6" class="px-5 py-10 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-project-diagram text-4xl mb-3"></i>
                                <p class="text-sm font-semibold">Tidak ada ODC ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-3.5">
            @forelse($odcs as $odc)
            <div class="mobile-card hover:shadow-xl transition-all duration-200">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-base shadow-md flex-shrink-0">
                        {{ substr($odc->kode_odc, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $odc->kode_odc }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $odc->nama }}</p>
                            </div>
                            @php
                                $usedPorts = $odc->direct_odps_count ?? 0;
                                $capacity = max(0, $odc->kapasitas_port);
                                $computedStatus = $capacity > 0 && $usedPorts >= $capacity ? 'penuh' : $odc->status;
                                $statusClass = match($computedStatus) {
                                    'aktif' => 'bg-emerald-50 text-emerald-600 border border-emerald-200',
                                    'penuh' => 'bg-amber-50 text-amber-600 border border-amber-200',
                                    'rusak' => 'bg-rose-50 text-rose-600 border border-rose-200',
                                    default => 'bg-gray-50 text-gray-600 border border-gray-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0 {{ $statusClass }}">
                                <i class="fas fa-circle mr-1 text-[7px]"></i>{{ ucfirst($computedStatus) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-2.5">
                        @php
                            $usedPorts = $odc->direct_odps_count ?? 0;
                            $capacity = max(0, $odc->kapasitas_port);
                            $usagePercent = $capacity > 0 ? min(100, ($usedPorts / $capacity) * 100) : 0;
                        @endphp
                        <p class="text-[10px] font-semibold text-indigo-600 mb-1">Port Terpakai</p>
                        <p class="text-xs font-semibold text-indigo-800 mb-1">{{ $usedPorts }}/{{ $capacity }} port</p>
                        <div class="w-full bg-indigo-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-1.5 rounded-full @if($usagePercent >= 100) bg-red-500 @elseif($usagePercent >= 80) bg-amber-500 @else bg-indigo-500 @endif" style="width: {{ $usagePercent }}%"></div>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-2.5">
                        <p class="text-[10px] font-semibold text-blue-600 mb-1">Jumlah ODP</p>
                        <p class="text-xs font-semibold text-blue-800">{{ $odc->total_odps_count ?? $odc->odps->count() }} ODP</p>
                    </div>
                    @if($odc->alamat)
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5 col-span-2">
                        <p class="text-[10px] font-semibold text-gray-500 mb-1">Alamat</p>
                        <p class="text-xs text-gray-900 line-clamp-2">{{ $odc->alamat }}</p>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('odcs.show', $odc) }}"
                       class="inline-flex flex-col items-center justify-center px-3.5 py-2 text-[11px] font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 text-center">
                        <i class="fas fa-eye mb-1 text-xs"></i>Detail
                    </a>
                    @can('manage-odp')
                    <a href="{{ route('odcs.edit', $odc) }}"
                       class="inline-flex flex-col items-center justify-center px-3.5 py-2 text-[11px] font-semibold bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 text-center">
                        <i class="fas fa-edit mb-1 text-xs"></i>Edit
                    </a>
                    <form action="{{ route('odcs.destroy', $odc) }}" method="POST" class="delete-form w-full" data-message="Yakin ingin menghapus ODC ini?">
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
                    <i class="fas fa-project-diagram text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tidak ada ODC</h3>
                <p class="text-sm text-gray-500 mb-6">Belum ada ODC yang terdaftar dalam sistem</p>
                @can('manage-odp')
                <a href="{{ route('odcs.create') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah ODC Pertama
                </a>
                @endcan
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($odcs->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $odcs->links() }}
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


