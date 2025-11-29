@extends('layouts.app')

@section('title', 'Tambah User - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah User Baru</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Buat akun pengguna baru untuk sistem WiFi Billing</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
        @csrf

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Informasi Dasar</p>
                <h2 class="text-base font-semibold text-gray-900">Data pengguna</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-indigo-500"></i>Nama Lengkap
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap pengguna"
                           required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-indigo-500"></i>Alamat Email
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white @error('email') border-red-500 @enderror"
                           placeholder="Masukkan alamat email pengguna"
                           required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-2 text-indigo-500"></i>Role Pengguna
                    </label>
                    <select name="role"
                            id="role"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white @error('role') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Role Pengguna</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Admin memiliki akses penuh, Penagih hanya dapat mengelola pelanggan yang ditugaskan
                    </p>
                </div>
            </div>
        </div>

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Keamanan</p>
                <h2 class="text-base font-semibold text-gray-900">Password</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-indigo-500"></i>Password
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password (minimal 8 karakter)"
                           required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-indigo-500"></i>Konfirmasi Password
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                           placeholder="Ulangi password untuk konfirmasi"
                           required>
                </div>
            </div>
        </div>

        <div class="app-card inline-actions">
            <a href="{{ route('users.index') }}"
               class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan User
            </button>
        </div>
    </form>
</div>
@endsection
