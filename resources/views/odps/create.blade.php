@extends('layouts.app')

@section('title', 'Tambah ODP - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah ODP Baru</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Definisikan ODP beserta lokasi koordinatnya</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('odps.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('odps.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

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
                           value="{{ old('kode_odp') }}"
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
                           value="{{ old('nama') }}"
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
                          placeholder="Alamat lengkap ODP">{{ old('alamat') }}</textarea>
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
                           value="{{ old('kapasitas', 0) }}"
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
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                        <option value="{{ $odc->id }}" {{ old('odc_id') == $odc->id ? 'selected' : '' }}>
                            {{ $odc->kode_odc }} - {{ $odc->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('odc_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parent_odp_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-link mr-2 text-purple-500"></i>Hubungkan ke ODP Terdekat
                    </label>
                    <select name="parent_odp_id"
                            id="parent_odp_id"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('parent_odp_id') border-red-500 @enderror">
                        <option value="">Pilih ODP Terdekat (opsional)</option>
                        @foreach($activeOdps as $activeOdp)
                        <option value="{{ $activeOdp->id }}" {{ old('parent_odp_id') == $activeOdp->id ? 'selected' : '' }}
                                data-lat="{{ $activeOdp->latitude }}"
                                data-lng="{{ $activeOdp->longitude }}">
                            {{ $activeOdp->kode_odp }} - {{ $activeOdp->nama }}
                            @if($activeOdp->odc)
                                (ODC: {{ $activeOdp->odc->kode_odc }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Pilih ODP aktif terdekat untuk menghubungkan ODP ini
                    </p>
                    @error('parent_odp_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="foto" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-image mr-2 text-purple-500"></i>Foto ODP
                </label>
                <input type="file"
                       name="foto"
                       id="foto"
                       accept="image/jpeg,image/png,image/jpg"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('foto') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
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
                           value="{{ old('latitude') }}"
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
                           value="{{ old('longitude') }}"
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
                    :default-lat="-6.49492336972348"
                    :default-lng="107.53623899978002"
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
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan ODP
            </button>
            <a href="{{ route('odps.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const odcSelect = document.getElementById('odc_id');
    const parentOdpSelect = document.getElementById('parent_odp_id');

    function validateExclusive() {
        if (odcSelect.value && parentOdpSelect.value) {
            // Jika keduanya terisi, kosongkan yang terakhir dipilih
            alert('Pilih salah satu: Hubungkan ke ODC atau Hubungkan ke ODP terdekat.');
            if (odcSelect.value) {
                parentOdpSelect.value = '';
            }
        }
    }

    odcSelect.addEventListener('change', function() {
        if (this.value && parentOdpSelect.value) {
            parentOdpSelect.value = '';
        }
    });

    parentOdpSelect.addEventListener('change', function() {
        if (this.value && odcSelect.value) {
            odcSelect.value = '';
        }
    });
});
</script>
@endpush
@endsection


