@extends('layouts.app')

@section('title', 'Manajemen MikroTik')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-server"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Manajemen MikroTik</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola router dan sinkronisasi user PPPoE</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('mikrotik.create') }}" 
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah Router
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Daftar Router</p>
                <h2 class="text-base font-semibold text-gray-900">Router Terdaftar</h2>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-network-wired mr-2"></i>Nama Router
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-globe mr-2"></i>IP Address
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-users mr-2"></i>Total User
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($routers as $router)
                    <tr class="hover:bg-indigo-50 transition border-b border-gray-100">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                    {{ substr($router->nama, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $router->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $router->location ?? 'Tidak ada lokasi' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-sm text-gray-700">
                            {{ $router->ip_address }}:{{ $router->port }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($router->connection_status == 'online')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
                                    Online
                                </span>
                            @elseif($router->connection_status == 'error')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-100 text-red-800 border border-red-200" title="{{ $router->last_error ?? 'Koneksi gagal' }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>
                                    Offline / Error
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="Klik tombol Test Koneksi untuk menguji status">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-amber-500 rounded-full"></span>
                                    Belum Dites
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
                                {{ $router->pppoe_users_count ?? 0 }} User
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-xs font-medium">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('mikrotik.show', $router->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('mikrotik.unmapped', $router->id) }}" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition" title="Belum Sinkron">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </a>
                                <a href="{{ route('mikrotik.edit', $router->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="sync-form-{{ $router->id }}" action="{{ route('mikrotik.sync', $router->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" onclick="confirmSync({{ $router->id }}, '{{ $router->nama }}')" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Sync Sekarang">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </form>
                                <button onclick="testConnection({{ $router->id }})" class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition" title="Test Connection">
                                    <i class="fas fa-plug"></i>
                                </button>
                                <form id="delete-form-{{ $router->id }}" action="{{ route('mikrotik.destroy', $router->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $router->id }}, '{{ $router->nama }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Router">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-server text-gray-300 text-4xl mb-3"></i>
                                <p>Belum ada router MikroTik yang ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Layout -->
        <div class="lg:hidden space-y-3">
            @forelse($routers as $router)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3 bg-white shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                        {{ substr($router->nama, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $router->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $router->ip_address }}</div>
                    </div>
                    <div class="ml-auto">
                        @if($router->connection_status == 'online')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800">Online</span>
                        @elseif($router->connection_status == 'error')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800" title="{{ $router->last_error ?? 'Koneksi gagal' }}">Offline</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">Belum Dites</span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <div class="font-semibold text-indigo-600">{{ $router->pppoe_users_count ?? 0 }}</div>
                        <div>User PPPoE</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <div class="font-semibold text-gray-900">{{ $router->port }}</div>
                        <div>Port API</div>
                    </div>
                </div>

                <div class="grid grid-cols-6 gap-1.5 text-[10px] font-semibold">
                    <a href="{{ route('mikrotik.show', $router->id) }}" class="col-span-1 flex flex-col items-center justify-center p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100">
                        <i class="fas fa-eye mb-1"></i>Detail
                    </a>
                    <a href="{{ route('mikrotik.unmapped', $router->id) }}" class="col-span-1 flex flex-col items-center justify-center p-2 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-100">
                        <i class="fas fa-link mb-1"></i>Map
                    </a>
                    <a href="{{ route('mikrotik.edit', $router->id) }}" class="col-span-1 flex flex-col items-center justify-center p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100">
                        <i class="fas fa-edit mb-1"></i>Edit
                    </a>
                    <button onclick="confirmSync({{ $router->id }}, '{{ $router->nama }}')" class="col-span-1 flex flex-col items-center justify-center p-2 rounded-xl bg-green-50 text-green-600 hover:bg-green-100">
                        <i class="fas fa-sync mb-1"></i>Sync
                    </button>
                    <button onclick="testConnection({{ $router->id }})" class="col-span-1 flex flex-col items-center justify-center p-2 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-plug mb-1"></i>Test
                    </button>
                    <button onclick="confirmDelete({{ $router->id }}, '{{ $router->nama }}')" class="col-span-1 flex flex-col items-center justify-center p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100">
                        <i class="fas fa-trash mb-1"></i>Hapus
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-server text-3xl mb-2 text-gray-300"></i>
                <p>Belum ada router.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmSync(id, name) {
        Swal.fire({
            title: 'Sinkronisasi Data?',
            text: `Tarik data PPPoE dan Sesi Aktif dari router "${name}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Sinkron Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#9CA3AF'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Sinkronisasi...',
                    text: 'Mohon tunggu sejenak.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById(`sync-form-${id}`).submit();
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus MikroTik?',
            text: `Apakah Anda yakin ingin menghapus router "${name}"? Semua data PPPoE terkait akan terhapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#9CA3AF'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function testConnection(id) {
        Swal.fire({
            title: 'Testing Connection...',
            text: 'Menghubungi router MikroTik via API...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/mikrotik/${id}/test-connection`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message, 'error').then(() => location.reload());
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Gagal menghubungi server', 'error').then(() => location.reload());
        });
    }
</script>
@endpush
@endsection
