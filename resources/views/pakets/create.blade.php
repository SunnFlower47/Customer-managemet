@extends('layouts.app')

@section('title', 'Tambah Paket - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah Paket Baru</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Definisikan paket internet beserta harga dan statusnya</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('pakets.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('pakets.store') }}" class="space-y-6">
        @csrf

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Informasi Paket</p>
                <h2 class="text-base font-semibold text-gray-900">Isi detail utama paket</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama_paket" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-blue-500"></i>Nama Paket
                    </label>
                    <input type="text"
                           name="nama_paket"
                           id="nama_paket"
                           value="{{ old('nama_paket') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-gray-50 focus:bg-white @error('nama_paket') border-red-500 @enderror"
                           placeholder="Misal: Paket 30 Mbps"
                           required>
                    @error('nama_paket')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>Harga
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">Rp</div>
                        <input type="number"
                               name="harga"
                               id="harga"
                               value="{{ old('harga') }}"
                               min="0"
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-semibold bg-gray-50 focus:bg-white @error('harga') border-red-500 @enderror"
                               placeholder="0"
                               required>
                    </div>
                    @error('harga')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2 text-blue-500"></i>Deskripsi
                </label>
                <textarea name="deskripsi"
                          id="deskripsi"
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-gray-50 focus:bg-white @error('deskripsi') border-red-500 @enderror"
                          placeholder="Tuliskan detail kecepatan, FUP, bonus, dll">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-xl px-4 py-3 flex items-start gap-3">
                <input type="checkbox"
                       name="aktif"
                       id="aktif"
                       value="1"
                       {{ old('aktif', true) ? 'checked' : '' }}
                       class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
            <label for="aktif" class="text-sm font-semibold text-gray-700">
                    Paket Aktif
                    <span class="block text-xs font-normal text-gray-500">Centang untuk langsung menampilkan paket ini di pilihan pelanggan</span>
                </label>
            </div>
        </div>

        <div class="app-card inline-actions">
            <a href="{{ route('pakets.index') }}"
               class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan Paket
            </button>
        </div>
    </form>
</div>
@endsection
