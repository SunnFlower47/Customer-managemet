@extends('layouts.app')

@section('title', 'MikroTik Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-server"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-indigo-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">MikroTik Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola dan monitor router MikroTik</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            @can('manage-mikrotik')
            <a href="{{ route('mikrotiks.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah MikroTik
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="app-card app-card--soft">
        <form method="GET" action="{{ route('mikrotiks.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nama, IP, atau lokasi"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Status
                </label>
                <select name="status"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Error</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="{{ route('mikrotiks.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- MikroTik List -->
    <div class="app-card">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Port</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">RouterOS</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Last Connected</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mikrotiks as $mikrotik)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-semibold text-base shadow-md flex-shrink-0 relative">
                                    <div class="absolute -top-1 -right-1 h-3 w-3 bg-indigo-500 rounded-full border-2 border-white"></div>
                                    <div class="flex items-center justify-center h-full">
                                        <i class="fas fa-server text-sm"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $mikrotik->nama }}</p>
                                    @if($mikrotik->location)
                                    <p class="text-xs text-gray-500">{{ $mikrotik->location }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-mono text-gray-900">{{ $mikrotik->ip_address }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900">{{ $mikrotik->port }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                {{ $mikrotik->routeros_version }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($mikrotik->connection_status === 'online')
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-circle text-[6px] mr-1"></i>Online
                            </span>
                            @elseif($mikrotik->connection_status === 'error' || $mikrotik->connection_status === 'offline')
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-800 border border-red-200" title="{{ $mikrotik->last_error ?? 'Koneksi gagal' }}">
                                <i class="fas fa-circle text-[6px] mr-1"></i>Offline / Error
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="Belum pernah di-test koneksi">
                                <i class="fas fa-circle text-[6px] mr-1"></i>Belum Dites
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($mikrotik->last_connected_at)
                            <p class="text-xs text-gray-600">{{ $mikrotik->last_connected_at->diffForHumans() }}</p>
                            @else
                            <p class="text-xs text-gray-400">Belum pernah</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('mikrotiks.show', $mikrotik) }}"
                                   class="px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('manage-mikrotik')
                                <a href="{{ route('mikrotiks.edit', $mikrotik) }}"
                                   class="px-3 py-1.5 text-xs font-semibold bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('mikrotiks.destroy', $mikrotik) }}" method="POST" class="inline delete-form"
                                      data-message="Yakin ingin menghapus router ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center">
                            <i class="fas fa-server text-gray-300 text-4xl mb-3"></i>
                            <p class="text-sm text-gray-500">Belum ada router MikroTik</p>
                            @can('manage-mikrotik')
                            <a href="{{ route('mikrotiks.create') }}"
                               class="inline-block mt-3 px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                                Tambah Router Pertama
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-3.5">
            @forelse($mikrotiks as $mikrotik)
            <div class="mobile-card hover:shadow-xl transition-all duration-200">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-semibold text-base shadow-md flex-shrink-0 relative">
                        <div class="absolute -top-1 -right-1 h-3 w-3 bg-indigo-500 rounded-full border-2 border-white"></div>
                        <div class="flex items-center justify-center h-full">
                            <i class="fas fa-server text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $mikrotik->nama }}</h3>
                        <p class="text-xs text-gray-500 font-mono">{{ $mikrotik->ip_address }}:{{ $mikrotik->port }}</p>
                        @if($mikrotik->location)
                        <p class="text-xs text-gray-500 mt-1">{{ $mikrotik->location }}</p>
                        @endif
                    </div>
                    <div>
                        @if($mikrotik->connection_status === 'online')
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold bg-green-100 text-green-800 border border-green-200">
                            <i class="fas fa-circle text-[4px] mr-1"></i>Online
                        </span>
                        @elseif($mikrotik->connection_status === 'error' || $mikrotik->connection_status === 'offline')
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold bg-red-100 text-red-800 border border-red-200">
                            <i class="fas fa-circle text-[4px] mr-1"></i>Offline
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <i class="fas fa-circle text-[4px] mr-1"></i>Belum Dites
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-server"></i>{{ $mikrotik->routeros_version }}
                        </span>
                        @if($mikrotik->last_connected_at)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock"></i>{{ $mikrotik->last_connected_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('mikrotiks.show', $mikrotik) }}"
                           class="px-2 py-1 text-[10px] font-semibold bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                            Detail
                        </a>
                        @can('manage-mikrotik')
                        <a href="{{ route('mikrotiks.edit', $mikrotik) }}"
                           class="px-2 py-1 text-[10px] font-semibold bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition">
                            Edit
                        </a>
                        <form action="{{ route('mikrotiks.destroy', $mikrotik) }}" method="POST" class="inline delete-form"
                              data-message="Yakin ingin menghapus router ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-2 py-1 text-[10px] font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                Hapus
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-server text-gray-300 text-4xl mb-3"></i>
                <p class="text-sm text-gray-500 mb-4">Belum ada router MikroTik</p>
                @can('manage-mikrotik')
                <a href="{{ route('mikrotiks.create') }}"
                   class="inline-block px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    Tambah Router Pertama
                </a>
                @endcan
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($mikrotiks->hasPages())
        <div class="mt-6">
            {{ $mikrotiks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formElement = this;
                
                let routerName = '';
                const tableRow = formElement.closest('tr');
                if (tableRow) {
                    routerName = tableRow.querySelector('td:first-child .text-sm')?.textContent.trim() || '';
                } else {
                    const cardContainer = formElement.closest('.mobile-card');
                    if (cardContainer) {
                        routerName = cardContainer.querySelector('h3')?.textContent.trim() || '';
                    }
                }

                Swal.fire({
                    title: 'Hapus MikroTik?',
                    text: routerName ? `Apakah Anda yakin ingin menghapus router "${routerName}"?` : 'Apakah Anda yakin ingin menghapus router ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#9CA3AF',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formElement.submit();
                    }
                });
            });
        });
    });
</script>
@endpush

