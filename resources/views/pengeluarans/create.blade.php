@extends('layouts.app')

@section('title', 'Tambah Pengeluaran - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah Pengeluaran</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Tambah data pengeluaran baru ke sistem</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('pengeluarans.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('pengeluarans.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-red-500 font-semibold">Informasi Dasar</p>
                <h2 class="text-base font-semibold text-gray-900">Data pengeluaran</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2 text-red-500"></i>Kategori Pengeluaran
                    </label>
                    <select name="kategori" id="kategori" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white @error('kategori') border-red-500 @enderror" required>
                        <option value="">Pilih Kategori Pengeluaran</option>
                        <option value="Operasional" {{ old('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="Pemeliharaan" {{ old('kategori') == 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                        <option value="Internet" {{ old('kategori') == 'Internet' ? 'selected' : '' }}>Internet</option>
                        <option value="Listrik" {{ old('kategori') == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="Gaji" {{ old('kategori') == 'Gaji' ? 'selected' : '' }}>Gaji</option>
                        <option value="Marketing" {{ old('kategori') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_pengeluaran" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-receipt mr-2 text-red-500"></i>Nama Pengeluaran
                    </label>
                    <input type="text" name="nama_pengeluaran" id="nama_pengeluaran" value="{{ old('nama_pengeluaran') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white @error('nama_pengeluaran') border-red-500 @enderror"
                           placeholder="Masukkan nama pengeluaran..." required>
                    @error('nama_pengeluaran')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_pengeluaran" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-red-500"></i>Tanggal Pengeluaran
                    </label>
                    <input type="date" name="tanggal_pengeluaran" id="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', date('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white @error('tanggal_pengeluaran') border-red-500 @enderror" required>
                    @error('tanggal_pengeluaran')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2 text-red-500"></i>Jumlah Pengeluaran (Rp)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">Rp</div>
                        <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-semibold bg-gray-50 focus:bg-white @error('jumlah') border-red-500 @enderror"
                               placeholder="0" min="0" step="100" required>
                    </div>
                    @error('jumlah')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2 text-red-500"></i>Deskripsi Pengeluaran
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="4"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white resize-none @error('deskripsi') border-red-500 @enderror"
                          placeholder="Masukkan deskripsi detail pengeluaran..." required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-red-500 font-semibold">Pembayaran & Status</p>
                <h2 class="text-base font-semibold text-gray-900">Metode dan status</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="metode_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2 text-red-500"></i>Metode Pembayaran
                    </label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white @error('metode_pembayaran') border-red-500 @enderror" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="kartu" {{ old('metode_pembayaran') == 'kartu' ? 'selected' : '' }}>Kartu Kredit/Debit</option>
                    </select>
                    @error('metode_pembayaran')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-red-500"></i>Status Pengeluaran
                    </label>
                    <select name="status" id="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white @error('status') border-red-500 @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="terkonfirmasi" {{ old('status') == 'terkonfirmasi' ? 'selected' : '' }}>Terkonfirmasi</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="bukti_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-paperclip mr-2 text-red-500"></i>Bukti Pembayaran (Opsional)
                </label>
                <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*,.pdf"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium bg-gray-50 focus:bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100 @error('bukti_pembayaran') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>Format yang didukung: JPG, PNG, PDF (Maksimal 2MB)
                </p>
                @error('bukti_pembayaran')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="app-card inline-actions">
            <a href="{{ route('pengeluarans.index') }}"
               class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan Pengeluaran
            </button>
        </div>
    </form>
</div>
@endsection
