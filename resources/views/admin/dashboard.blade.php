@extends('layouts.app')

@section('title', 'Customer Portal - Admin Dashboard')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-globe"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Customer Portal Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola dan pantau aktivitas customer portal</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="https://customer.andrinet.id" target="_blank"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-external-link-alt mr-2 text-xs sm:text-sm"></i>Buka Portal
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="stat-card">
            <div class="stat-card__icon bg-blue-50 text-blue-600">
                <i class="fas fa-users"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pelanggan</p>
                <p class="stat-card__value">{{ \App\Models\Pelanggan::count() }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card__icon bg-green-50 text-green-600">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan Aktif</p>
                <p class="stat-card__value">{{ \App\Models\Pelanggan::where('status', 'aktif')->count() }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card__icon bg-amber-50 text-amber-600">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tiket Pending</p>
                <p class="stat-card__value">{{ \App\Models\Ticket::where('status', 'pending')->count() ?? 0 }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card__icon bg-purple-50 text-purple-600">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Bukti Pembayaran</p>
                <p class="stat-card__value">{{ \App\Models\PaymentProof::where('status', 'pending')->count() ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="app-card space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Tiket Terbaru</p>
                    <h2 class="text-base font-semibold text-gray-900">Aktivitas support terbaru</h2>
                </div>
                <a href="{{ route('admin.tickets.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat semua →
                </a>
            </div>
            <div class="space-y-2">
                @forelse(\App\Models\Ticket::with('pelanggan')->latest()->take(5)->get() as $ticket)
                <div class="mobile-card border border-gray-200 rounded-xl px-3 py-2.5">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-ticket-alt text-amber-600 text-xs"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->judul ?? 'No Subject' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $ticket->pelanggan->nama ?? 'N/A' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold
                            @if($ticket->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                            @elseif($ticket->status === 'resolved') bg-green-100 text-green-800 border border-green-200
                            @else bg-red-100 text-red-800 border border-red-200 @endif">
                            {{ ucfirst($ticket->status ?? 'unknown') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <i class="fas fa-ticket-alt text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada tiket</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="app-card space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Bukti Pembayaran Terbaru</p>
                    <h2 class="text-base font-semibold text-gray-900">Verifikasi pembayaran</h2>
                </div>
                <a href="{{ route('admin.payment-proofs.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat semua →
                </a>
            </div>
            <div class="space-y-2">
                @forelse(\App\Models\PaymentProof::with(['pelanggan', 'pembayaran'])->latest()->take(5)->get() as $proof)
                <div class="mobile-card border border-gray-200 rounded-xl px-3 py-2.5">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-receipt text-green-600 text-xs"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($proof->pembayaran->jumlah ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $proof->pelanggan->nama ?? 'N/A' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold
                            @if($proof->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                            @elseif($proof->status === 'verified') bg-green-100 text-green-800 border border-green-200
                            @else bg-red-100 text-red-800 border border-red-200 @endif">
                            {{ ucfirst($proof->status ?? 'unknown') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <i class="fas fa-receipt text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada bukti pembayaran</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="app-card space-y-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Akses Cepat</p>
            <h2 class="text-base font-semibold text-gray-900">Menu customer portal</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @can('view-ticket')
            <a href="{{ route('admin.tickets.index') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-ticket-alt text-amber-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-900">Kelola Tiket</h4>
                    <p class="text-xs text-gray-500">Lihat dan kelola tiket pelanggan</p>
                </div>
            </a>
            @endcan

            @can('view-payment-proof')
            <a href="{{ route('admin.payment-proofs.index') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <div class="h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-receipt text-green-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-900">Verifikasi Pembayaran</h4>
                    <p class="text-xs text-gray-500">Verifikasi bukti pembayaran pelanggan</p>
                </div>
            </a>
            @endcan

            <a href="https://customer.barayacitramandiri.net" target="_blank" class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-external-link-alt text-blue-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-900">Customer Portal</h4>
                    <p class="text-xs text-gray-500">Buka portal pelanggan</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
