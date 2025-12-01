@extends('layouts.app')

@section('title', 'Edit ODP - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Edit ODP</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Perbarui informasi ODP dan lokasinya</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('odps.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('odps.update', $odp) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Informasi ODP</p>
                <h2 class="text-base font-semibold text-gray-900">Isi detail utama ODP</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kode_odp" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-barcode mr-2 text-purple-500"></i>Kode ODP <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="kode_odp"
                           id="kode_odp"
                           value="{{ old('kode_odp', $odp->kode_odp) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('kode_odp') border-red-500 @enderror"
                           placeholder="Misal: ODP-001"
                           required>
                    @error('kode_odp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-purple-500"></i>Nama ODP <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $odp->nama) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('nama') border-red-500 @enderror"
                           placeholder="Misal: ODP Jalan Raya"
                           required>
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Alamat
                </label>
                <textarea name="alamat"
                          id="alamat"
                          rows="2"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('alamat') border-red-500 @enderror"
                          placeholder="Alamat lengkap ODP">{{ old('alamat', $odp->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kapasitas" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-plug mr-2 text-purple-500"></i>Kapasitas Port <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="kapasitas"
                           id="kapasitas"
                           value="{{ old('kapasitas', $odp->kapasitas) }}"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-semibold bg-gray-50 focus:bg-white @error('kapasitas') border-red-500 @enderror"
                           placeholder="0"
                           required>
                    @error('kapasitas')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-purple-500"></i>Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('status') border-red-500 @enderror"
                            required>
                        <option value="aktif" {{ old('status', $odp->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $odp->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="odc_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-project-diagram mr-2 text-purple-500"></i>Terhubung ke ODC
                    </label>
                    <select name="odc_id"
                            id="odc_id"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('odc_id') border-red-500 @enderror">
                        <option value="">Pilih ODC (opsional)</option>
                        @foreach($odcs as $odc)
                        <option value="{{ $odc->id }}" {{ old('odc_id', $odp->odc_id) == $odc->id ? 'selected' : '' }}>
                            {{ $odc->kode_odc }} - {{ $odc->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('odc_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="foto" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-image mr-2 text-purple-500"></i>Foto ODP
                </label>
                @if($odp->foto)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $odp->foto) }}" alt="Foto ODP" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                </div>
                @endif
                <input type="file"
                       name="foto"
                       id="foto"
                       accept="image/jpeg,image/png,image/jpg"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('foto') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</p>
                @error('foto')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Lokasi Koordinat -->
        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Lokasi Koordinat</p>
                <h2 class="text-base font-semibold text-gray-900">Pilih lokasi ODP di peta atau input manual</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-latitude mr-2 text-purple-500"></i>Latitude <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="latitude"
                           id="latitude"
                           value="{{ old('latitude', $odp->latitude) }}"
                           step="any"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('latitude') border-red-500 @enderror"
                           placeholder="-7.123456"
                           required>
                    @error('latitude')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-longitude mr-2 text-purple-500"></i>Longitude <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="longitude"
                           id="longitude"
                           value="{{ old('longitude', $odp->longitude) }}"
                           step="any"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('longitude') border-red-500 @enderror"
                           placeholder="110.123456"
                           required>
                    @error('longitude')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map mr-2 text-purple-500"></i>Pilih dari Peta
                </label>
                <x-map-picker 
                    map-id="map-picker"
                    latitude-input-id="latitude"
                    longitude-input-id="longitude"
                    :default-lat="$odp->koordinat_latitude ?? -6.49492336972348"
                    :default-lng="$odp->koordinat_longitude ?? 107.53623899978002"
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
            <a href="{{ route('odps.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>
@endsection

