@extends('layouts.app')

@section('title', 'Edit Pelanggan - WiFi Billing Management')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Pelanggan</h1>
            <p class="mt-2 text-sm text-gray-700">Edit data pelanggan {{ $pelanggan->nama }}.</p>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('pelanggans.update', $pelanggan) }}" class="space-y-6 p-6">
                @csrf
                @method('PUT')
                
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
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $pelanggan->nama) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nama') border-red-300 @enderror">
                        @error('nama')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PPPoE -->
                    <div>
                        <label for="pppoe" class="block text-sm font-medium text-gray-700">PPPoE</label>
                        <input type="text" name="pppoe" id="pppoe" value="{{ old('pppoe', $pelanggan->pppoe) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('pppoe') border-red-300 @enderror">
                        @error('pppoe')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('no_hp') border-red-300 @enderror">
                        @error('no_hp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Paket -->
                    <div>
                        <label for="paket_id" class="block text-sm font-medium text-gray-700">Paket</label>
                        <select name="paket_id" id="paket_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('paket_id') border-red-300 @enderror">
                            <option value="">Pilih Paket</option>
                            @foreach($pakets as $paket)
                                <option value="{{ $paket->id }}" {{ old('paket_id', $pelanggan->paket_id) == $paket->id ? 'selected' : '' }}>
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
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', $pelanggan->tanggal_mulai ? $pelanggan->tanggal_mulai->format('Y-m-d') : '') }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tanggal_mulai') border-red-300 @enderror">
                        @error('tanggal_mulai')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pembayaran -->
                    <div>
                        <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                        <select name="tanggal_pembayaran" id="tanggal_pembayaran" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tanggal_pembayaran') border-red-300 @enderror">
                            <option value="">Pilih Tanggal</option>
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}" {{ old('tanggal_pembayaran', $pelanggan->tanggal_pembayaran) == $i ? 'selected' : '' }}>
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
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('penagih_id') border-red-300 @enderror">
                            <option value="">Pilih Penagih</option>
                            @foreach($penagihs as $penagih)
                                <option value="{{ $penagih->id }}" {{ old('penagih_id', $pelanggan->penagih_id) == $penagih->id ? 'selected' : '' }}>
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
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status') border-red-300 @enderror">
                            <option value="aktif" {{ old('status', $pelanggan->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $pelanggan->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="suspend" {{ old('status', $pelanggan->status) === 'suspend' ? 'selected' : '' }}>Suspend</option>
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
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('alamat') border-red-300 @enderror">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('pelanggans.index', request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id'])) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
