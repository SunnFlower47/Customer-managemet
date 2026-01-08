@extends('layouts.app')

@section('title', 'Edit MikroTik')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Edit Router</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Perbarui konfigurasi router {{ $mikrotik->nama }}</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('mikrotik.index') }}" 
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-300 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="app-card">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h3 class="text-base font-semibold text-gray-900">Edit Informasi Koneksi</h3>
            <p class="text-sm text-gray-500 mt-1">Perbarui detail kredensial router Anda</p>
        </div>

        <form action="{{ route('mikrotik.update', $mikrotik->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Identifikasi
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $mikrotik->nama) }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- IP & Port -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        IP Address
                    </label>
                    <input type="text" name="ip_address" value="{{ old('ip_address', $mikrotik->ip_address) }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    @error('ip_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Port API
                    </label>
                    <input type="number" name="port" value="{{ old('port', $mikrotik->port) }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    @error('port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Auth -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Username
                    </label>
                    <input type="text" name="username" value="{{ old('username', $mikrotik->username) }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password"
                               placeholder="Biarkan kosong jika tidak ingin mengubah"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
