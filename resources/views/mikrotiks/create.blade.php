@extends('layouts.app')

@section('title', 'Tambah MikroTik')

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
                <h1 class="page-header__title text-slate-900">Tambah MikroTik</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Tambahkan router MikroTik baru ke sistem</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('mikrotiks.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Panduan Koneksi -->
    <div class="app-card bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between gap-2 sm:gap-4 text-left hover:opacity-80 transition py-3 sm:py-2.5">
            <div class="flex items-center gap-2.5 sm:gap-4 flex-1 min-w-0">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-info-circle text-white text-base sm:text-xl"></i>
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Panduan</h3>
                    <p class="text-xs sm:text-sm text-gray-700 mt-0.5 leading-tight">Menyambungkan ke MikroTik</p>
                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1 leading-tight">Klik untuk melihat panduan lengkap</p>
                </div>
            </div>
            <div class="flex-shrink-0 ml-2">
                <i class="fas fa-chevron-down text-gray-400 text-sm sm:text-base transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </div>
        </button>

        <div x-show="open" x-cloak x-transition class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-blue-200">
            <div class="space-y-3 sm:space-y-4 text-xs sm:text-sm text-gray-700">
                <div>
                    <p class="font-semibold mb-1.5 sm:mb-2 text-xs sm:text-sm">1. Pastikan Router MikroTik dapat diakses dari server ini:</p>
                    <ul class="list-disc list-inside ml-2 sm:ml-3 space-y-1 text-gray-600 text-xs sm:text-sm leading-relaxed">
                        <li>Router harus dalam jaringan yang sama atau dapat diakses via internet</li>
                        <li>Port API (default: 8728) harus terbuka dan tidak di-block firewall</li>
                        <li>Test koneksi dengan ping ke IP Address router</li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold mb-1.5 sm:mb-2 text-xs sm:text-sm">2. Aktifkan API di Router MikroTik:</p>
                    <div class="bg-white rounded-lg p-2.5 sm:p-3 mt-1 font-mono text-[10px] sm:text-xs border border-blue-200 overflow-x-auto">
                        <p class="text-gray-800 whitespace-nowrap">/ip service</p>
                        <p class="text-gray-800 whitespace-nowrap">set api disabled=no port=8728</p>
                    </div>
                </div>
                <div>
                    <p class="font-semibold mb-1.5 sm:mb-2 text-xs sm:text-sm">3. Buat user dengan permission API:</p>
                    <div class="bg-white rounded-lg p-2.5 sm:p-3 mt-1 font-mono text-[10px] sm:text-xs border border-blue-200 overflow-x-auto">
                        <p class="text-gray-800 whitespace-nowrap break-all">/user add name=api-user password=your-password group=full</p>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-600 mt-1.5">Atau gunakan user admin yang sudah ada</p>
                </div>
                <div>
                    <p class="font-semibold mb-1.5 sm:mb-2 text-xs sm:text-sm">4. Informasi yang diperlukan:</p>
                    <ul class="list-disc list-inside ml-2 sm:ml-3 space-y-1 text-gray-600 text-xs sm:text-sm leading-relaxed">
                        <li><strong>IP Address:</strong> IP router (contoh: 192.168.1.1)</li>
                        <li><strong>Port:</strong> Port API (default: 8728 untuk RouterOS v6/v7)</li>
                        <li><strong>Username:</strong> Username yang punya akses API</li>
                        <li><strong>Password:</strong> Password user tersebut</li>
                        <li><strong>RouterOS Version:</strong> Versi RouterOS yang digunakan</li>
                    </ul>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2.5 sm:p-3 mt-3">
                    <p class="text-[10px] sm:text-xs font-semibold text-yellow-800 flex items-center gap-2 mb-1.5">
                        <i class="fas fa-exclamation-triangle text-xs"></i>
                        <span>Tips:</span>
                    </p>
                    <ul class="text-[10px] sm:text-xs text-yellow-700 ml-4 sm:ml-5 list-disc space-y-1 leading-relaxed">
                        <li>Setelah menyimpan, sistem akan otomatis test koneksi</li>
                        <li>Jika koneksi gagal, periksa firewall dan pastikan port API terbuka</li>
                        <li>Untuk RouterOS v7+, pastikan API service aktif di menu Services</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('mikrotiks.store') }}" method="POST" class="app-card space-y-6">
        @csrf

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
                           value="{{ old('nama') }}"
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
                           value="{{ old('location') }}"
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
                           value="{{ old('ip_address') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono @error('ip_address') border-red-500 @enderror"
                           placeholder="192.168.1.1">
                    <p class="mt-1 text-xs text-gray-500">IP Address router MikroTik yang dapat diakses dari server</p>
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
                           value="{{ old('port', 8728) }}"
                           required
                           min="1"
                           max="65535"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('port') border-red-500 @enderror"
                           placeholder="8728">
                    <p class="mt-1 text-xs text-gray-500">Port API RouterOS (default: 8728 untuk v6/v7)</p>
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
                        <option value="v7" {{ old('routeros_version', 'v7') === 'v7' ? 'selected' : '' }}>v7</option>
                        <option value="v7.1+" {{ old('routeros_version') === 'v7.1+' ? 'selected' : '' }}>v7.1+</option>
                        <option value="v6" {{ old('routeros_version') === 'v6' ? 'selected' : '' }}>v6</option>
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
                           value="{{ old('username') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                           placeholder="admin">
                    <p class="mt-1 text-xs text-gray-500">Username yang memiliki akses API (biasanya "admin" atau user dengan group "full")</p>
                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
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
                      placeholder="Deskripsi router (opsional)">{{ old('description') }}</textarea>
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
                <i class="fas fa-save mr-2"></i>Simpan & Test Koneksi
            </button>
        </div>
    </form>
</div>
@endsection

