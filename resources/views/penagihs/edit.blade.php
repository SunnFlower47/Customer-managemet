@extends('layouts.app')

@section('title', 'Edit Penagih - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Edit Penagih</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Edit data penagih: {{ $penagih->nama }}</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('penagihs.show', $penagih) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-eye mr-2 text-xs sm:text-sm"></i>Detail
            </a>
            <a href="{{ route('penagihs.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('penagihs.update', $penagih) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Informasi Dasar</p>
                <h2 class="text-base font-semibold text-gray-900">Perbarui data penagih</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-indigo-500"></i>Nama Lengkap
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $penagih->nama) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-indigo-500"></i>Email
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $penagih->email) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('email') border-red-500 @enderror"
                           placeholder="Masukkan email"
                           required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-indigo-500"></i>No. HP
                    </label>
                    <input type="text"
                           name="no_hp"
                           id="no_hp"
                           value="{{ old('no_hp', $penagih->no_hp) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('no_hp') border-red-500 @enderror"
                           placeholder="Masukkan nomor HP">
                    @error('no_hp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>Alamat
                    </label>
                    <textarea name="alamat"
                              id="alamat"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat">{{ old('alamat', $penagih->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        @if($penagih->user)
        <div class="app-card space-y-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Akun User</p>
                <h2 class="text-base font-semibold text-gray-900">Informasi akun terhubung</h2>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Username</span>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $penagih->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email Login</span>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $penagih->user->email }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 mt-1">
                            {{ ucfirst($penagih->user->role) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status Akun</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold mt-1 {{ $penagih->user->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $penagih->user->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="app-card">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-xl px-4 py-3 flex items-start gap-3">
                <input type="checkbox"
                       name="aktif"
                       id="aktif"
                       value="1"
                       {{ old('aktif', $penagih->aktif) ? 'checked' : '' }}
                       class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="aktif" class="text-sm font-semibold text-gray-700">
                    Penagih Aktif
                    <span class="block text-xs font-normal text-gray-500 mt-1">Centang untuk mengaktifkan penagih ini</span>
                </label>
            </div>
        </div>

        <div class="app-card inline-actions">
            <a href="{{ route('penagihs.index') }}"
               class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
