@extends('layouts.app')

@section('title', 'Edit MikroTik')

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
                <h1 class="page-header__title text-slate-900">Edit MikroTik</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Edit informasi router MikroTik</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('mikrotiks.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('mikrotiks.update', $mikrotik) }}" method="POST" class="app-card space-y-6">
        @csrf
        @method('PUT')

        <!-- Informasi Dasar -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Router <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $mikrotik->nama) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Router Utama">
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                        Lokasi
                    </label>
                    <input type="text"
                           name="location"
                           id="location"
                           value="{{ old('location', $mikrotik->location) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                           placeholder="Contoh: Kantor Pusat">
                    @error('location')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Koneksi -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-4">Koneksi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="ip_address" class="block text-sm font-semibold text-gray-700 mb-2">
                        IP Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="ip_address"
                           id="ip_address"
                           value="{{ old('ip_address', $mikrotik->ip_address) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono @error('ip_address') border-red-500 @enderror"
                           placeholder="192.168.1.1">
                    @error('ip_address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="port" class="block text-sm font-semibold text-gray-700 mb-2">
                        Port <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="port"
                           id="port"
                           value="{{ old('port', $mikrotik->port) }}"
                           required
                           min="1"
                           max="65535"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('port') border-red-500 @enderror"
                           placeholder="8728">
                    @error('port')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="routeros_version" class="block text-sm font-semibold text-gray-700 mb-2">
                        RouterOS Version <span class="text-red-500">*</span>
                    </label>
                    <select name="routeros_version"
                            id="routeros_version"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('routeros_version') border-red-500 @enderror">
                        <option value="v7" {{ old('routeros_version', $mikrotik->routeros_version) === 'v7' ? 'selected' : '' }}>v7</option>
                        <option value="v7.1+" {{ old('routeros_version', $mikrotik->routeros_version) === 'v7.1+' ? 'selected' : '' }}>v7.1+</option>
                        <option value="v6" {{ old('routeros_version', $mikrotik->routeros_version) === 'v6' ? 'selected' : '' }}>v6</option>
                    </select>
                    @error('routeros_version')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Autentikasi -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-4">Autentikasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="username"
                           id="username"
                           value="{{ old('username', $mikrotik->username) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                           placeholder="admin">
                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-gray-500 text-xs">(Kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password baru">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status -->
        <div>
            <label class="flex items-center gap-3">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       {{ old('is_active', $mikrotik->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <span class="text-sm font-semibold text-gray-700">Aktif</span>
            </label>
        </div>

        <!-- Deskripsi -->
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea name="description"
                      id="description"
                      rows="3"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                      placeholder="Deskripsi router (opsional)">{{ old('description', $mikrotik->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('mikrotiks.index') }}"
               class="px-6 py-3 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-center">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

