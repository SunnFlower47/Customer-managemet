@extends('layouts.app')

@section('title', 'Tambah User - WiFi Billing Management')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-user-plus mr-2 text-blue-600"></i>Tambah User Baru
            </h1>
            <p class="mt-1 text-sm text-gray-600">Buat akun pengguna baru untuk sistem WiFi Billing</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1 text-gray-400"></i>Nama Lengkap
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1 text-gray-400"></i>Email
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('email') border-red-500 @enderror"
                           placeholder="Masukkan alamat email"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-1 text-gray-400"></i>Role
                    </label>
                    <select name="role"
                            id="role"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('role') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Admin memiliki akses penuh, Penagih hanya dapat mengelola pelanggan yang ditugaskan
                    </p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1 text-gray-400"></i>Password
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password (minimal 8 karakter)"
                           required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1 text-gray-400"></i>Konfirmasi Password
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Ulangi password"
                           required>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('users.index') }}"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
