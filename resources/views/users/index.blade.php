@extends('layouts.app')

@section('title', 'User Management - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola pengguna sistem WiFi Billing</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah User
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1 text-gray-400"></i>Search
                </label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                       placeholder="Cari nama atau email...">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-tag mr-1 text-gray-400"></i>Role
                </label>
                <select name="role"
                        id="role"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="penagih" {{ request('role') === 'penagih' ? 'selected' : '' }}>Penagih</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-users mr-2 text-blue-600"></i>Daftar User
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $users->total() }} total)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user mr-1"></i>User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-envelope mr-1"></i>Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user-tag mr-1"></i>Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-1"></i>Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-gray-600">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user-tie' }} mr-1"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200"
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada user</h3>
                                <p class="text-gray-500 mb-4">Belum ada user yang terdaftar dalam sistem.</p>
                                <a href="{{ route('users.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah User Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
        @endif
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
            const userName = form.closest('tr').querySelector('td:first-child').textContent.trim();
            console.log('User to delete:', userName);

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
