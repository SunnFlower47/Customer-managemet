@extends('layouts.app')

@section('title', 'Edit ODC - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Edit ODC</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Perbarui informasi ODC dan lokasinya</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('odcs.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('odcs.update', $odc) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Informasi ODC</p>
                <h2 class="text-base font-semibold text-gray-900">Isi detail utama ODC</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kode_odc" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-barcode mr-2 text-indigo-500"></i>Kode ODC <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="kode_odc"
                           id="kode_odc"
                           value="{{ old('kode_odc', $odc->kode_odc) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('kode_odc') border-red-500 @enderror"
                           placeholder="Misal: ODC-001"
                           required>
                    @error('kode_odc')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-indigo-500"></i>Nama ODC <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $odc->nama) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('nama') border-red-500 @enderror"
                           placeholder="Misal: ODC Central"
                           required>
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kapasitas_port" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-plug mr-2 text-indigo-500"></i>Kapasitas Port <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="kapasitas_port"
                           id="kapasitas_port"
                           value="{{ old('kapasitas_port', $odc->kapasitas_port) }}"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold bg-gray-50 focus:bg-white @error('kapasitas_port') border-red-500 @enderror"
                           placeholder="0"
                           required>
                    @error('kapasitas_port')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('status') border-red-500 @enderror"
                            required>
                        <option value="aktif" {{ old('status', $odc->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="penuh" {{ old('status', $odc->status) === 'penuh' ? 'selected' : '' }}>Penuh</option>
                        <option value="rusak" {{ old('status', $odc->status) === 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>Alamat
                </label>
                <textarea name="alamat"
                          id="alamat"
                          rows="2"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('alamat') border-red-500 @enderror"
                          placeholder="Alamat lengkap ODC">{{ old('alamat', $odc->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2 text-indigo-500"></i>Keterangan
                </label>
                <textarea name="keterangan"
                          id="keterangan"
                          rows="2"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('keterangan') border-red-500 @enderror"
                          placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $odc->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Lokasi Koordinat -->
        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Lokasi Koordinat</p>
                <h2 class="text-base font-semibold text-gray-900">Pilih lokasi ODC di peta atau input manual (opsional)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-latitude mr-2 text-indigo-500"></i>Latitude
                    </label>
                    <input type="number"
                           name="latitude"
                           id="latitude"
                           value="{{ old('latitude', $odc->latitude) }}"
                           step="any"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('latitude') border-red-500 @enderror"
                           placeholder="-7.123456">
                    @error('latitude')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-longitude mr-2 text-indigo-500"></i>Longitude
                    </label>
                    <input type="number"
                           name="longitude"
                           id="longitude"
                           value="{{ old('longitude', $odc->longitude) }}"
                           step="any"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium bg-gray-50 focus:bg-white @error('longitude') border-red-500 @enderror"
                           placeholder="110.123456">
                    @error('longitude')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map mr-2 text-indigo-500"></i>Pilih dari Peta
                </label>
                <x-map-picker 
                    map-id="odc-map-picker"
                    latitude-input-id="latitude"
                    longitude-input-id="longitude"
                    :default-lat="$odc->latitude ?? -6.49492336972348"
                    :default-lng="$odc->longitude ?? 107.53623899978002"
                    :zoom="15"
                    :draggable="true"
                    :show-satellite-toggle="true"
                />
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>Klik di peta untuk mengatur koordinat, atau drag marker untuk menyesuaikan
                </p>
            </div>
        </div>

        <div class="app-card inline-actions">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('odcs.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>
@endsection


