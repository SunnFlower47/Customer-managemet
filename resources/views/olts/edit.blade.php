@extends('layouts.app')

@section('title', 'Edit OLT')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit OLT</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">{{ $olt->kode_olt }} - {{ $olt->nama }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" x-data x-on:click="$dispatch('open-guide-olt-create')" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-book-open mr-2"></i>Panduan
            </button>
            <a href="{{ route('olts.show', $olt) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm" x-data="{ connectionType: '{{ old('connection_type', $olt->connection_type) }}' }">
        <form method="POST" action="{{ route('olts.update', $olt) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-800 mb-2">Terdapat kesalahan:</p>
                        <ul class="text-xs text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @include('olts.partials.form')

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('olts.show', $olt) }}" class="px-5 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<x-guide-panel key="olt-create" title="Panduan Edit OLT">
    <div class="space-y-3 text-sm text-gray-600">
        <div>
            <p class="font-semibold text-gray-900 mb-1">1. Informasi Dasar</p>
            <p>Perbarui kode OLT, nama, atau IP address jika diperlukan. Pastikan kode OLT tetap unik.</p>
        </div>
        <div>
            <p class="font-semibold text-gray-900 mb-1">2. Koneksi & Akses</p>
            <p>Ubah jenis koneksi atau kredensial jika ada perubahan. Untuk password, kosongkan jika tidak ingin mengubahnya.</p>
        </div>
        <div>
            <p class="font-semibold text-gray-900 mb-1">3. Verifikasi</p>
            <p>Setelah menyimpan, sistem akan otomatis melakukan tes koneksi untuk memastikan perubahan berhasil.</p>
        </div>
    </div>
</x-guide-panel>
@endsection
