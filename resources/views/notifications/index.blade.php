@extends('layouts.app')

@section('title', 'Notifikasi Sistem - BCM WiFi')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Notifikasi Sistem</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Ringkasan tiket pelanggan dan bukti pembayaran pending</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        @can('view-ticket')
        <div class="app-card space-y-4">
            <div class="flex flex-col gap-1">
                <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Pelaporan Tiket</p>
                <h2 class="text-base font-semibold text-gray-900">Tiket open dari pelanggan</h2>
                <p class="text-xs text-gray-500">{{ $ticketCount }} tiket menunggu penanganan</p>
            </div>
            <div class="space-y-3">
                @forelse($ticketPaginator as $ticket)
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="mobile-card border border-gray-200 rounded-2xl block">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-ticket-alt text-amber-600 text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->kode_ticket }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $ticket->pelanggan->nama ?? '-' }} • {{ $ticket->created_at->diffForHumans() }}</p>
                                <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-tag text-[10px]"></i>{{ ucfirst($ticket->kategori) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i>{{ ucfirst($ticket->prioritas) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="app-card border border-dashed border-gray-200 text-center py-8">
                        <p class="text-sm text-gray-500">Tidak ada tiket open.</p>
                    </div>
                @endforelse
            </div>
            {{ $ticketPaginator->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
        @endcan

        @can('view-payment-proof')
        <div class="app-card space-y-4">
            <div class="flex flex-col gap-1">
                <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Bukti Pembayaran</p>
                <h2 class="text-base font-semibold text-gray-900">Verifikasi pending</h2>
                <p class="text-xs text-gray-500">{{ $paymentProofCount }} bukti menunggu verifikasi</p>
            </div>
            <div class="space-y-3">
                @forelse($paymentProofPaginator as $proof)
                    <a href="{{ route('admin.payment-proofs.show', $proof) }}" class="mobile-card border border-gray-200 rounded-2xl block">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-receipt text-green-600 text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $proof->pembayaran->kode_pembayaran ?? '-' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $proof->pelanggan->nama ?? '-' }} • {{ $proof->created_at->diffForHumans() }}</p>
                                <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-upload text-[10px]"></i>{{ ucfirst(str_replace('_', ' ', $proof->submission_method ?? '-')) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 font-semibold text-green-600">
                                        <i class="fas fa-money-bill-wave text-[10px]"></i>
                                        Rp {{ number_format($proof->pembayaran->harga_paket ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="app-card border border-dashed border-gray-200 text-center py-8">
                        <p class="text-sm text-gray-500">Tidak ada bukti pembayaran pending.</p>
                    </div>
                @endforelse
            </div>
            {{ $paymentProofPaginator->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
        @endcan
    </div>

    @cannot('view-ticket')
        @cannot('view-payment-proof')
            <div class="app-card border border-dashed border-gray-200 text-center py-8">
                <p class="text-sm text-gray-500">Anda tidak memiliki akses untuk melihat notifikasi.</p>
            </div>
        @endcannot
    @endcannot
</div>
@endsection

