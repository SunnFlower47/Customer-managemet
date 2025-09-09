@extends('layouts.app')

@section('title', 'Tambah Pelanggan - WiFi Billing Management')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Tambah Pelanggan</h1>
            <p class="mt-2 text-sm text-gray-700">Tambahkan pelanggan baru ke sistem.</p>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('pelanggans.store') }}" class="space-y-6 p-6">
                @csrf
                
                <!-- Hidden fields to preserve pagination and filters -->
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
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('nama') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap...">
                        @error('nama')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PPPoE -->
                    <div>
                        <label for="pppoe" class="block text-sm font-medium text-gray-700">PPPoE</label>
                        <input type="text" name="pppoe" id="pppoe" value="{{ old('pppoe') }}" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('pppoe') border-red-500 @enderror"
                            placeholder="Masukkan username PPPoE...">
                        @error('pppoe')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('no_hp') border-red-500 @enderror"
                            placeholder="Masukkan nomor HP...">
                        @error('no_hp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Paket -->
                    <div>
                        <label for="paket_id" class="block text-sm font-medium text-gray-700">Paket</label>
                        <select name="paket_id" id="paket_id" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('paket_id') border-red-500 @enderror">
                            <option value="">Pilih Paket</option>
                            @foreach($pakets as $paket)
                                <option value="{{ $paket->id }}" {{ old('paket_id') == $paket->id ? 'selected' : '' }}>
                                    {{ $paket->nama_paket }} - Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('paket_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('tanggal_mulai') border-red-500 @enderror">
                        @error('tanggal_mulai')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pembayaran -->
                    <div>
                        <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                        <select name="tanggal_pembayaran" id="tanggal_pembayaran" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('tanggal_pembayaran') border-red-500 @enderror">
                            <option value="">Pilih Tanggal</option>
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}" {{ old('tanggal_pembayaran', 1) == $i ? 'selected' : '' }}>
                                    Tanggal {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('tanggal_pembayaran')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penagih -->
                    <div>
                        <label for="penagih_id" class="block text-sm font-medium text-gray-700">Penagih</label>
                        <select name="penagih_id" id="penagih_id" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('penagih_id') border-red-500 @enderror">
                            <option value="">Pilih Penagih</option>
                            @foreach($penagihs as $penagih)
                                <option value="{{ $penagih->id }}" {{ old('penagih_id') == $penagih->id ? 'selected' : '' }}>
                                    {{ $penagih->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('penagih_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('status') border-red-500 @enderror">
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="suspend" {{ old('status') === 'suspend' ? 'selected' : '' }}>Suspend</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" required
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('alamat') border-red-500 @enderror"
                        placeholder="Masukkan alamat lengkap...">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('pelanggans.index', request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id'])) }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
