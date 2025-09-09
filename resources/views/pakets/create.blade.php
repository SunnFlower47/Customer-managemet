@extends('layouts.app')

@section('title', 'Tambah Paket - WiFi Billing Management')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle mr-2 text-blue-600"></i>Tambah Paket Baru
                </h1>
            </div>

            <form method="POST" action="{{ route('pakets.store') }}" class="p-6">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="nama_paket" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1 text-gray-400"></i>Nama Paket
                        </label>
                        <input type="text"
                               name="nama_paket"
                               id="nama_paket"
                               value="{{ old('nama_paket') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('nama_paket') border-red-500 @enderror"
                               placeholder="Masukkan nama paket"
                               required>
                        @error('nama_paket')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign mr-1 text-gray-400"></i>Harga
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number"
                                   name="harga"
                                   id="harga"
                                   value="{{ old('harga') }}"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('harga') border-red-500 @enderror"
                                   placeholder="0"
                                   min="0"
                                   required>
                        </div>
                        @error('harga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-1 text-gray-400"></i>Deskripsi
                        </label>
                        <textarea name="deskripsi"
                                  id="deskripsi"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('deskripsi') border-red-500 @enderror"
                                  placeholder="Masukkan deskripsi paket">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox"
                               name="aktif"
                               id="aktif"
                               value="1"
                               {{ old('aktif', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="aktif" class="ml-2 block text-sm text-gray-700">
                            <i class="fas fa-check-circle mr-1 text-green-500"></i>Paket Aktif
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('pakets.index') }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Simpan Paket
                    </button>
                </div>
            </form>
    </div>
</div>
@endsection
