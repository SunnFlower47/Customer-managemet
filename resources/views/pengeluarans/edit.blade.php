@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Pengeluaran</h1>
        <p class="mt-1 text-sm text-gray-600">Edit data pengeluaran</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('pengeluarans.update', $pengeluaran) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="kategori" id="kategori" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('kategori') border-red-500 @enderror" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Operasional" {{ old('kategori', $pengeluaran->kategori) == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="Pemeliharaan" {{ old('kategori', $pengeluaran->kategori) == 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                        <option value="Internet" {{ old('kategori', $pengeluaran->kategori) == 'Internet' ? 'selected' : '' }}>Internet</option>
                        <option value="Listrik" {{ old('kategori', $pengeluaran->kategori) == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="Gaji" {{ old('kategori', $pengeluaran->kategori) == 'Gaji' ? 'selected' : '' }}>Gaji</option>
                        <option value="Marketing" {{ old('kategori', $pengeluaran->kategori) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="Lainnya" {{ old('kategori', $pengeluaran->kategori) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">Nama Pengeluaran</label>
                    <input type="text" name="nama_pengeluaran" id="nama_pengeluaran" value="{{ old('nama_pengeluaran', $pengeluaran->nama_pengeluaran) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('nama_pengeluaran') border-red-500 @enderror"
                           placeholder="Masukkan nama pengeluaran..." required>
                    @error('nama_pengeluaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal_pengeluaran" id="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', $pengeluaran->tanggal_pengeluaran) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('tanggal_pengeluaran') border-red-500 @enderror" required>
                    @error('tanggal_pengeluaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('deskripsi') border-red-500 @enderror"
                              placeholder="Masukkan deskripsi pengeluaran..." required>{{ old('deskripsi', $pengeluaran->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $pengeluaran->jumlah) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('jumlah') border-red-500 @enderror"
                           placeholder="0" min="0" step="100" required>
                    @error('jumlah')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('pengeluarans.show', $pengeluaran) }}"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
