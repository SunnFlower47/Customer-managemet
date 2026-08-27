@extends('layouts.app')

@section('title', 'Edit Pelanggan - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="page-header">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-white text-xl"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Edit Pelanggan</h1>
                <p class="mt-1 text-sm text-gray-600">Perbarui data {{ $pelanggan->nama }}</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('pelanggans.show', $pelanggan) }}"
               class="inline-flex items-center px-4 py-2 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="app-card space-y-6">
        <form method="POST" action="{{ route('pelanggans.update', $pelanggan) }}" class="space-y-6" id="editForm" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-indigo-600"></i>Nama Lengkap
                    </label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $pelanggan->nama) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('nama') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap...">
                    <x-form-error field="nama" />
                </div>

                <div>
                    <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-indigo-600"></i>NIK (Opsional)
                    </label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik', $pelanggan->nik) }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('nik') border-red-500 @enderror"
                        placeholder="Masukkan 16 digit NIK...">
                    <x-form-error field="nik" />
                </div>

                <div>
                    <label for="pppoe" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-wifi mr-2 text-indigo-600"></i>PPPoE
                    </label>
                    <input type="text" name="pppoe" id="pppoe" value="{{ old('pppoe', $pelanggan->pppoe) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('pppoe') border-red-500 @enderror"
                        placeholder="Masukkan username PPPoE...">
                    <x-form-error field="pppoe" />
                </div>

                <!-- Serial Number STB -->
                <div>
                    <label for="serial_number_stb" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tv mr-2 text-indigo-600"></i>No. Serial STB <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" name="serial_number_stb" id="serial_number_stb" value="{{ old('serial_number_stb', $pelanggan->serial_number_stb) }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white font-mono @error('serial_number_stb') border-red-500 @enderror"
                        placeholder="Masukkan serial number STB...">
                    <x-form-error field="serial_number_stb" />
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-indigo-600"></i>No. HP
                    </label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('no_hp') border-red-500 @enderror"
                        placeholder="Masukkan nomor HP...">
                    @error('no_hp')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="paket_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box mr-2 text-indigo-600"></i>Paket
                    </label>
                    <select name="paket_id" id="paket_id"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('paket_id') border-red-500 @enderror">
                        <option value="">Pilih Paket</option>
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->id }}" {{ old('paket_id', $pelanggan->paket_id) == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }} - Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('paket_id')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>Tanggal Mulai
                    </label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', $pelanggan->tanggal_mulai ? $pelanggan->tanggal_mulai->format('Y-m-d') : '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('tanggal_mulai') border-red-500 @enderror">
                    @error('tanggal_mulai')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-2 text-indigo-600"></i>Tanggal Pembayaran
                    </label>
                    <select name="tanggal_pembayaran" id="tanggal_pembayaran" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('tanggal_pembayaran') border-red-500 @enderror">
                        <option value="">Pilih Tanggal</option>
                        @for($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}" {{ old('tanggal_pembayaran', $pelanggan->tanggal_pembayaran) == $i ? 'selected' : '' }}>
                                Tanggal {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('tanggal_pembayaran')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="penagih_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tie mr-2 text-indigo-600"></i>Penagih
                    </label>
                    <select name="penagih_id" id="penagih_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('penagih_id') border-red-500 @enderror">
                        <option value="">Pilih Penagih</option>
                        @foreach($penagihs as $penagih)
                            <option value="{{ $penagih->id }}" {{ old('penagih_id', $pelanggan->penagih_id) == $penagih->id ? 'selected' : '' }}>
                                {{ $penagih->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('penagih_id')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-indigo-600"></i>Status
                    </label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('status') border-red-500 @enderror">
                        <option value="aktif" {{ old('status', $pelanggan->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="isolir" {{ old('status', $pelanggan->status) === 'isolir' ? 'selected' : '' }}>Isolir</option>
                        <option value="bayar double" {{ old('status', $pelanggan->status) === 'bayar double' ? 'selected' : '' }}>Bayar Double</option>
                        <option value="nonaktif" {{ old('status', $pelanggan->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-indigo-600"></i>Alamat
                </label>
                <textarea name="alamat" id="alamat" rows="4" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm font-medium bg-gray-50 focus:bg-white resize-none @error('alamat') border-red-500 @enderror"
                    placeholder="Masukkan alamat lengkap...">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi Koordinat (Opsional) -->
            <div class="border-t border-gray-200 pt-6 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold mb-1">Lokasi Koordinat (Opsional)</p>
                    <p class="text-xs text-gray-500">Koordinat dapat ditambahkan atau diubah di sini atau di halaman Mapping</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-latitude mr-2 text-green-500"></i>Latitude
                        </label>
                        <input type="number"
                               name="latitude"
                               id="latitude"
                               value="{{ old('latitude', $pelanggan->latitude) }}"
                               step="any"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium bg-gray-50 focus:bg-white @error('latitude') border-red-500 @enderror"
                               placeholder="-7.123456">
                        @error('latitude')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-longitude mr-2 text-green-500"></i>Longitude
                        </label>
                        <input type="number"
                               name="longitude"
                               id="longitude"
                               value="{{ old('longitude', $pelanggan->longitude) }}"
                               step="any"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium bg-gray-50 focus:bg-white @error('longitude') border-red-500 @enderror"
                               placeholder="110.123456">
                        @error('longitude')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map mr-2 text-green-500"></i>Pilih dari Peta (Opsional)
                    </label>
                    <x-map-picker 
                        map-id="location-picker-map"
                        latitude-input-id="latitude"
                        longitude-input-id="longitude"
                        :default-lat="$pelanggan->latitude ?? -6.49492336972348"
                        :default-lng="$pelanggan->longitude ?? 107.53623899978002"
                        :zoom="15"
                        :draggable="true"
                        :show-satellite-toggle="true"
                    />
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Klik di peta untuk mengatur koordinat, atau drag marker untuk menyesuaikan
                    </p>
                </div>
                <div>
                    <label for="odp_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>ODP Terkait (Opsional)
                    </label>
                    <select name="odp_id"
                            id="odp_id"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('odp_id') border-red-500 @enderror">
                        <option value="">Pilih ODP (Opsional)</option>
                        @foreach(\App\Models\Odp::active()->orderBy('nama')->get() as $odp)
                        <option value="{{ $odp->id }}" {{ old('odp_id', $pelanggan->odp_id) == $odp->id ? 'selected' : '' }}>
                            {{ $odp->kode_odp }} - {{ $odp->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('odp_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('pelanggans.show', $pelanggan) }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-sm font-semibold">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" 
                    :disabled="submitting"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-spinner fa-spin mr-2" x-show="submitting" x-cloak></i>
                    <i class="fas fa-save mr-2" x-show="!submitting"></i>
                    <span x-text="submitting ? 'Menyimpan...' : 'Update Pelanggan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

