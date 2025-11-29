@extends('layouts.app')

@section('title', 'Detail Pengeluaran - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $pengeluaran->nama_pengeluaran }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Detail pengeluaran operasional</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold bg-red-50 text-red-600 border border-red-100 rounded-xl">
                {{ $pengeluaran->formatted_jumlah }}
            </span>
            @if((auth()->user()?->role ?? 'guest') === 'admin')
            <a href="{{ route('pengeluarans.edit', $pengeluaran) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            @endif
            <a href="{{ route('pengeluarans.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="app-card space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-red-500 font-semibold">Informasi Pengeluaran</p>
                        <h2 class="text-base font-semibold text-gray-900">Detail lengkap</h2>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d F Y') }}</dd>
                    </div>
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-1">Jumlah</dt>
                        <dd class="text-xl font-bold text-red-900">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategori</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $pengeluaran->kategori }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Metode Pembayaran</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ ucfirst($pengeluaran->metode_pembayaran) }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $pengeluaran->status_badge_class }}">
                                {{ ucfirst($pengeluaran->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $pengeluaran->deskripsi }}</dd>
                </div>
            </div>
        </div>

        <div>
            <div class="app-card space-y-4">
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Informasi Sistem</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-user text-blue-500"></i>Dibuat Oleh</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $pengeluaran->user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-calendar text-blue-500"></i>Dibuat Pada</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $pengeluaran->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($pengeluaran->updated_at != $pengeluaran->created_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-edit text-blue-500"></i>Terakhir Diupdate</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $pengeluaran->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if((auth()->user()?->role ?? 'guest') === 'admin')
            <div class="app-card mt-6">
                <form method="POST" action="{{ route('pengeluarans.destroy', $pengeluaran) }}" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                        <i class="fas fa-trash mr-2"></i>Hapus Pengeluaran
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.querySelector('.delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Pengeluaran?',
                text: 'Apakah Anda yakin ingin menghapus pengeluaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }
});
</script>
@endpush
@endsection
