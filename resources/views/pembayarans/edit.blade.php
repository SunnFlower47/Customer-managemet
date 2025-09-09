@extends('layouts.app')

@section('title', 'Edit Pembayaran - WiFi Billing Management')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-edit mr-2 text-blue-600"></i>Edit Pembayaran
            </h1>
            <p class="mt-1 text-sm text-gray-600">Perbarui informasi pembayaran untuk {{ $pembayaran->pelanggan->nama }}</p>
        </div>

        <form method="POST" action="{{ route('pembayarans.update', $pembayaran) }}" class="p-6">
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
            @if(request('bulan'))
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
            @endif
            @if(request('tahun'))
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
            @endif

            <div class="space-y-6">
                <!-- Customer Information (Read-only) -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-user mr-2 text-gray-400"></i>Informasi Pelanggan
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold text-sm">{{ substr($pembayaran->pelanggan->nama, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-lg font-semibold text-gray-900">{{ $pembayaran->pelanggan->nama }}</p>
                                <p class="text-sm text-gray-500">{{ $pembayaran->pelanggan->pppoe }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-receipt mr-2 text-gray-400"></i>Informasi Pembayaran
                    </h3>
                    <div class="space-y-4">
                        <!-- IMMUTABLE FIELDS - Read Only -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-1 text-gray-400"></i>Bulan Tagihan
                                    <span class="text-xs text-gray-500 ml-2">(Tidak dapat diubah)</span>
                                </label>
                                <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F') }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>Tahun Tagihan
                                    <span class="text-xs text-gray-500 ml-2">(Tidak dapat diubah)</span>
                                </label>
                                <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    {{ $pembayaran->tahun_tagihan }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-dollar-sign mr-1 text-gray-400"></i>Jumlah Tagihan
                                    <span class="text-xs text-orange-500 ml-2">(Dapat diubah untuk perbaikan)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number"
                                           name="jumlah"
                                           id="jumlah"
                                           value="{{ old('jumlah', $pembayaran->jumlah) }}"
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('jumlah') border-red-500 @enderror"
                                           placeholder="0"
                                           min="0"
                                           required>
                                </div>
                                @error('jumlah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-info-circle mr-1 text-gray-400"></i>Status Pembayaran
                                </label>
                                <select name="status"
                                        id="status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="belum_bayar" {{ old('status', $pembayaran->status) === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="lunas" {{ old('status', $pembayaran->status) === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-1 text-gray-400"></i>Tanggal Bayar
                            </label>
                            <input type="datetime-local"
                                   name="tanggal_bayar"
                                   id="tanggal_bayar"
                                   value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('tanggal_bayar') border-red-500 @enderror">
                            @error('tanggal_bayar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1 text-gray-400"></i>Keterangan
                            </label>
                            <textarea name="keterangan"
                                      id="keterangan"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('keterangan') border-red-500 @enderror"
                                      placeholder="Masukkan keterangan pembayaran">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('pembayarans.index', request()->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun'])) }}"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Update Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-update tanggal_bayar when status changes to 'lunas'
document.getElementById('status').addEventListener('change', function() {
    const tanggalBayarField = document.getElementById('tanggal_bayar');
    if (this.value === 'lunas' && !tanggalBayarField.value) {
        // Set current date and time if not already set
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        tanggalBayarField.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    } else if (this.value === 'belum_bayar') {
        // Clear tanggal_bayar if status is 'belum_bayar'
        tanggalBayarField.value = '';
    }
});
</script>
@endsection