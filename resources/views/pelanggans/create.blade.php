@extends('layouts.app')

@section('title', 'Tambah Pelanggan - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="page-header">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah Pelanggan</h1>
                <p class="mt-1 text-sm text-gray-600">Masukkan data pelanggan baru dengan lengkap</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('pelanggans.index', request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id'])) }}"
               class="inline-flex items-center px-4 py-2 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="app-card space-y-6">
        <form method="POST" action="{{ route('pelanggans.store') }}" class="space-y-6" id="createForm" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf

            @if(request('page'))
                <input type="hidden" name="page" value="{{ request('page') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('penagih_id'))
                <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
            @endif
            @if(request('paket_id'))
                <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-600"></i>Nama Lengkap
                        </label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('nama') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap...">
                        <x-form-error field="nama" />
                    </div>

                    <!-- PPPoE -->
                    <div>
                        <label for="pppoe" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-wifi mr-2 text-blue-600"></i>PPPoE
                        </label>
                        <input type="text" name="pppoe" id="pppoe" value="{{ old('pppoe') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('pppoe') border-red-500 @enderror"
                            placeholder="Masukkan username PPPoE...">
                        <x-form-error field="pppoe" />
                    </div>

                    <!-- Serial Number STB -->

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-blue-600"></i>No. HP
                        </label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('no_hp') border-red-500 @enderror"
                            placeholder="Masukkan nomor HP...">
                        <x-form-error field="no_hp" />
                    </div>

                    <!-- Paket -->
                    <div>
                        <label for="paket_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-box mr-2 text-blue-600"></i>Paket
                        </label>
                        <select name="paket_id" id="paket_id"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('paket_id') border-red-500 @enderror">
                            <option value="">Pilih Paket</option>
                            @foreach($pakets as $paket)
                                <option value="{{ $paket->id }}" {{ old('paket_id') == $paket->id ? 'selected' : '' }}>
                                    {{ $paket->nama_paket }} - Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('paket_id')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('tanggal_mulai') border-red-500 @enderror">
                        @error('tanggal_mulai')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pembayaran -->
                    <div>
                        <label for="tanggal_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-2 text-blue-600"></i>Tanggal Pembayaran
                        </label>
                        <select name="tanggal_pembayaran" id="tanggal_pembayaran" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('tanggal_pembayaran') border-red-500 @enderror">
                            <option value="">Pilih Tanggal</option>
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}" {{ old('tanggal_pembayaran', 1) == $i ? 'selected' : '' }}>
                                    Tanggal {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('tanggal_pembayaran')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penagih -->
                    <div>
                        <label for="penagih_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-tie mr-2 text-blue-600"></i>Penagih
                        </label>
                        <select name="penagih_id" id="penagih_id" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('penagih_id') border-red-500 @enderror">
                            <option value="">Pilih Penagih</option>
                            @foreach($penagihs as $penagih)
                                <option value="{{ $penagih->id }}" {{ old('penagih_id') == $penagih->id ? 'selected' : '' }}>
                                    {{ $penagih->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('penagih_id')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Status
                        </label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white @error('status') border-red-500 @enderror">
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="isolir" {{ old('status') === 'isolir' ? 'selected' : '' }}>Isolir</option>
                            <option value="bayar double" {{ old('status') === 'bayar double' ? 'selected' : '' }}>Bayar Double</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Alamat
                    </label>
                <textarea name="alamat" id="alamat" rows="4" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm font-medium bg-gray-50 focus:bg-white resize-none @error('alamat') border-red-500 @enderror"
                    placeholder="Masukkan alamat lengkap...">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
            </div>

            <!-- Lokasi Koordinat (Opsional) -->
            <div class="border-t border-gray-200 pt-6 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold mb-1">Lokasi Koordinat (Opsional)</p>
                    <p class="text-xs text-gray-500">Koordinat dapat ditambahkan nanti di halaman Mapping</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-latitude mr-2 text-green-500"></i>Latitude
                        </label>
                        <input type="number"
                               name="latitude"
                               id="latitude"
                               value="{{ old('latitude') }}"
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
                               value="{{ old('longitude') }}"
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

                <div>
                    <label for="odp_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>ODP Terkait (Opsional)
                    </label>
                    <select name="odp_id"
                            id="odp_id"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('odp_id') border-red-500 @enderror">
                        <option value="">Pilih ODP (Opsional)</option>
                        @foreach(\App\Models\Odp::active()->orderBy('nama')->get() as $odp)
                        <option value="{{ $odp->id }}" {{ old('odp_id') == $odp->id ? 'selected' : '' }}>
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
                <a href="{{ route('pelanggans.index', request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id'])) }}"
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-sm font-semibold">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-spinner fa-spin mr-2" x-show="submitting" x-cloak></i>
                    <i class="fas fa-save mr-2" x-show="!submitting"></i>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Pelanggan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

