@extends('layouts.app')

@section('title', 'Detail Pengeluaran')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Detail Pengeluaran</h1>
        <p class="mt-1 text-sm text-gray-600">Informasi detail pengeluaran</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <p class="text-lg font-bold text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                <p class="text-lg text-gray-900">{{ $pengeluaran->user->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Pada</label>
                <p class="text-lg text-gray-900">{{ $pengeluaran->created_at->format('d F Y H:i') }}</p>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900 whitespace-pre-line">{{ $pengeluaran->deskripsi }}</p>
            </div>
        </div>

        @if($pengeluaran->updated_at != $pengeluaran->created_at)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Diupdate</label>
                <p class="text-lg text-gray-900">{{ $pengeluaran->updated_at->format('d F Y H:i') }}</p>
            </div>
        @endif
    </div>

    <div class="flex justify-end space-x-4 mt-6">
        <a href="{{ route('pengeluarans.index') }}"
           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>

        @if((auth()->user()?->role ?? 'guest') === 'admin')
            <a href="{{ route('pengeluarans.edit', $pengeluaran) }}"
               class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition duration-200 font-medium">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>

            <form method="POST" action="{{ route('pengeluarans.destroy', $pengeluaran) }}" class="inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
