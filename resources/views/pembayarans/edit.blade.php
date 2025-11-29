@extends('layouts.app')

@section('title', 'Edit Pembayaran - WiFi Billing Management')

@section('content')
@php
    $dueDate = \Carbon\Carbon::create(
        $pembayaran->tahun_tagihan,
        $pembayaran->bulan_tagihan,
        $pembayaran->pelanggan->tanggal_pembayaran
    );
@endphp

<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Edit Pembayaran</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Perbarui nominal, status, dan catatan pembayaran pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('pembayarans.show', $pembayaran) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-eye mr-2 text-xs sm:text-sm"></i>Detail
            </a>
            <a href="{{ route('pembayarans.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('pembayarans.update', $pembayaran) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun'] as $param)
            @if(request($param))
                <input type="hidden" name="{{ $param }}" value="{{ request($param) }}">
            @endif
        @endforeach

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="app-card space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Informasi Pelanggan</p>
                            <h2 class="text-base font-semibold text-gray-900">Profil (read-only)</h2>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                            PPPoE {{ $pembayaran->pelanggan->pppoe }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-lg flex items-center justify-center shadow">
                            {{ substr($pembayaran->pelanggan->nama, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-lg font-semibold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-phone"></i>{{ $pembayaran->pelanggan->no_hp ?: '-' }}
                            </p>
                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-map-marker-alt"></i>{{ $pembayaran->pelanggan->alamat ?: '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="app-card space-y-5">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Detail Pembayaran</p>
                        <h2 class="text-base font-semibold text-gray-900">Perbarui data tagihan</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-calendar"></i>Bulan Tagihan
                            </span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 block">
                                {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }}
                            </span>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-calendar-alt"></i>Tahun Tagihan
                            </span>
                            <span class="text-sm font-semibold text-gray-900 mt-1 block">{{ $pembayaran->tahun_tagihan }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>Jumlah Tagihan
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">Rp</div>
                                <input type="number" name="jumlah" id="jumlah" min="0"
                                       value="{{ old('jumlah', $pembayaran->jumlah) }}"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-semibold @error('jumlah') border-red-500 @enderror"
                                       placeholder="0" required>
                            </div>
                            @error('jumlah')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Status Pembayaran
                            </label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold @error('status') border-red-500 @enderror"
                                    required>
                                <option value="belum_bayar" {{ old('status', $pembayaran->status) === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="lunas" {{ old('status', $pembayaran->status) === 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_bayar" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clock mr-2 text-gray-400"></i>Tanggal Bayar
                            </label>
                            <input type="datetime-local" name="tanggal_bayar" id="tanggal_bayar"
                                   value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold @error('tanggal_bayar') border-red-500 @enderror">
                            @error('tanggal_bayar')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-comment mr-2 text-gray-400"></i>Keterangan
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium @error('keterangan') border-red-500 @enderror"
                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="app-card space-y-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        Metadata
                    </span>
                    <dl class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span class="font-medium flex items-center gap-2">
                                <i class="fas fa-user-tie text-purple-500"></i>Penagih
                            </span>
                            <span class="text-gray-900 font-semibold text-right">
                                @if($pembayaran->nama_penagih)
                                    {{ $pembayaran->nama_penagih }}
                                @elseif($pembayaran->penagih)
                                    {{ $pembayaran->penagih->nama }}
                                @else
                                    <span class="text-gray-400 italic">Belum ditentukan</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium flex items-center gap-2">
                                <i class="fas fa-calendar-check text-green-500"></i>Jatuh Tempo
                            </span>
                            <span class="text-gray-900 font-semibold text-right">{{ $dueDate->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium flex items-center gap-2">
                                <i class="fas fa-history text-indigo-500"></i>Diperbarui
                            </span>
                            <span class="text-gray-900 font-semibold text-right">{{ $pembayaran->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="app-card inline-actions">
            <a href="{{ route('pembayarans.index', request()->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun'])) }}"
               class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusField = document.getElementById('status');
    const tanggalBayarField = document.getElementById('tanggal_bayar');

    if (!statusField || !tanggalBayarField) return;

    statusField.addEventListener('change', function () {
        if (this.value === 'lunas' && !tanggalBayarField.value) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            tanggalBayarField.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        if (this.value === 'belum_bayar') {
            tanggalBayarField.value = '';
        }
    });
});
</script>
@endpush
@endsection
