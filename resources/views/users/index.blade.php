@extends('layouts.app')

@section('title', 'User Management - {{ \App\Models\CompanyProfile::first()->initials ?? "BCM" }} WiFi Billing')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">User Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Kelola pengguna sistem WiFi Billing</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <div class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white">
                <i class="fas fa-info-circle mr-2 text-indigo-500"></i>{{ $users->total() }} user
            </div>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah User
            </a>
        </div>
    </div>

    <div class="app-card space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-indigo-500"></i>Filter & Pencarian
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-indigo-500"></i>
                Cari dan filter pengguna
            </p>
        </div>
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-indigo-500"></i>Pencarian
                </label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                       placeholder="Cari berdasarkan nama atau email...">
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user-tag mr-2 text-indigo-500"></i>Role Pengguna
                </label>
                <select name="role"
                        id="role"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="penagih" {{ request('role') === 'penagih' ? 'selected' : '' }}>Penagih</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Daftar Pengguna</p>
                <h2 class="text-base font-semibold text-gray-900">Manajemen user sistem</h2>
            </div>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Pengguna
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user-tag mr-2"></i>Role
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Dibuat
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-indigo-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 text-white text-sm font-bold flex items-center justify-center shadow">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700 truncate max-w-xs" title="{{ $user->email }}">
                                <i class="fas fa-envelope mr-1 text-gray-400"></i>{{ $user->email }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user-tie' }} mr-1 text-[9px]"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700">
                                <i class="fas fa-calendar mr-1 text-gray-400"></i>{{ $user->created_at->format('d M Y') }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-xs font-medium">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-indigo-600 hover:text-indigo-900 transition"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-500">Tidak ada user</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($users->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        <div class="lg:hidden space-y-2">
            @forelse($users as $user)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 text-white text-sm font-bold flex items-center justify-center shadow">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }} ml-auto">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <div class="mt-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Dibuat:</span>
                        <span class="text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-3 gap-2 text-[11px] font-semibold">
                    <a href="{{ route('users.show', $user) }}" class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-eye mb-1"></i>Detail
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-md transition">
                        <i class="fas fa-edit mb-1"></i>Edit
                    </a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex flex-col items-center justify-center px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-md transition">
                            <i class="fas fa-trash mb-1"></i>Hapus
                        </button>
                    </form>
                    @else
                    <div class="inline-flex flex-col items-center justify-center px-3 py-2 bg-gray-100 text-gray-400 rounded-xl">
                        <i class="fas fa-lock mb-1"></i>Self
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="app-card text-center py-12">
                <div class="flex flex-col items-center">
                    <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada user</h3>
                    <p class="text-sm text-gray-500">Belum ada user yang terdaftar dalam sistem.</p>
                </div>
            </div>
            @endforelse

            @if($users->hasPages())
                <div class="mt-4">
                    {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Users page loaded, initializing SweetAlert...');

    // Handle delete confirmation with SweetAlert
    const deleteForms = document.querySelectorAll('.delete-form');
    console.log('Found delete forms:', deleteForms.length);

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Delete form submitted');

            const form = this;
            let userName = '';
            const tableRow = form.closest('tr');
            if (tableRow) {
                const nameElement = tableRow.querySelector('td:first-child .font-semibold');
                if (nameElement) {
                    userName = nameElement.textContent.trim();
                }
            } else {
                const cardContainer = form.closest('.mobile-card');
                if (cardContainer) {
                    const nameElement = cardContainer.querySelector('.font-semibold');
                    if (nameElement) {
                        userName = nameElement.textContent.trim();
                    }
                }
            }

            Swal.fire({
                title: 'Hapus User?',
                text: `Apakah Anda yakin ingin menghapus user "${userName}"?`,
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
