@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $greetIcon = $hour < 12 ? ':sunny:' : ($hour < 15 ? ':sun:' : ($hour < 18 ? ':city_sunset:' : ':moon:'));
    $totalPelanggan = \App\Models\Pelanggan::count();
    $aktifPelanggan = \App\Models\Pelanggan::whereIn('status', ['aktif', 'bayar double'])->count();
    $tiketPending   = \App\Models\Ticket::where('status', 'pending')->count();
    $buktiPending   = \App\Models\PaymentProof::where('status', 'pending')->count();
@endphp
<div class="space-y-6 lg:space-y-7">
    {{-- GREETING HEADER --}}
    <div class="relative overflow-hidden rounded-2xl p-5 sm:p-6" style="background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 100%);">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-200 text-sm font-medium mb-1">{{ $greetIcon }} {{ $greeting }}, {{ Auth::user()->name ?? 'Admin' }}!</p>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Dashboard Customer Portal</h1>
                <p class="text-blue-300 text-xs sm:text-sm mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <a href="https://customer.andrinet.id" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25 border border-white/20 text-white text-sm font-semibold rounded-xl transition-all self-start sm:self-auto">
                <i class="fas fa-external-link-alt text-xs"></i>Buka Portal
            </a>
        </div>
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
        <div class="absolute bottom-0 right-16 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 pointer-events-none"></div>
    </div>
    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <span class="text-[10px] font-bold text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full">TOTAL</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ number_format($totalPelanggan) }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">Total Pelanggan</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <span class="text-[10px] font-bold text-green-500 bg-green-50 px-2 py-0.5 rounded-full">AKTIF</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ number_format($aktifPelanggan) }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">Pelanggan Aktif</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-amber-600"></i>
                </div>
                @if($tiketPending > 0)
                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">{{ $tiketPending }} baru</span>
                @else
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">kosong</span>
                @endif
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ number_format($tiketPending) }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">Tiket Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="h-10 w-10 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-purple-600"></i>
                </div>
                @if($buktiPending > 0)
                    <span class="text-[10px] font-bold text-purple-600 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-full">{{ $buktiPending }} baru</span>
                @else
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">kosong</span>
                @endif
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ number_format($buktiPending) }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">Bukti Pending</p>
        </div>
    </div>
    {{-- TIKET & BUKTI PEMBAYARAN --}}
    <div class="grid gap-5 lg:grid-cols-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 bg-amber-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Tiket Terbaru</h2>
                        <p class="text-[11px] text-gray-400">Aktivitas support pelanggan</p>
                    </div>
                </div>
                <a href="{{ route('admin.tickets.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Lihat semua &rarr;</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse(\App\Models\Ticket::with('pelanggan')->latest()->take(5)->get() as $ticket)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                    <div class="h-8 w-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-ticket-alt text-amber-500 text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->judul ?? 'No Subject' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $ticket->pelanggan->nama ?? 'N/A' }}</p>
                    </div>
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold @if($ticket->status === 'pending') bg-yellow-100 text-yellow-700 border border-yellow-200 @elseif($ticket->status === 'resolved') bg-green-100 text-green-700 border border-green-200 @else bg-red-100 text-red-700 border border-red-200 @endif">
                        {{ ucfirst($ticket->status ?? '-') }}
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="h-12 w-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-ticket-alt text-gray-300 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada tiket</p>
                </div>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-green-500 text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Bukti Pembayaran</h2>
                        <p class="text-[11px] text-gray-400">Verifikasi pembayaran pelanggan</p>
                    </div>
                </div>
                <a href="{{ route('admin.payment-proofs.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Lihat semua &rarr;</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse(\App\Models\PaymentProof::with(['pelanggan', 'pembayaran'])->latest()->take(5)->get() as $proof)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                    <div class="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-receipt text-green-500 text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($proof->pembayaran->jumlah ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $proof->pelanggan->nama ?? 'N/A' }}</p>
                    </div>
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold @if($proof->status === 'pending') bg-yellow-100 text-yellow-700 border border-yellow-200 @elseif($proof->status === 'verified') bg-green-100 text-green-700 border border-green-200 @else bg-red-100 text-red-700 border border-red-200 @endif">
                        {{ ucfirst($proof->status ?? '-') }}
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="h-12 w-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-receipt text-gray-300 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada bukti pembayaran</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    {{-- QUICK ACCESS --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="mb-4">
            <p class="text-[11px] uppercase tracking-widest text-blue-500 font-bold mb-0.5">Akses Cepat</p>
            <h2 class="text-sm font-bold text-gray-900">Menu Customer Portal</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @can('view-ticket')
            <a href="{{ route('admin.tickets.index') }}" class="flex items-center gap-3 p-3.5 border border-gray-100 bg-gray-50/50 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition-all group">
                <div class="h-10 w-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 transition-colors">
                    <i class="fas fa-ticket-alt text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Kelola Tiket</p>
                    <p class="text-xs text-gray-400">Lihat dan kelola tiket</p>
                </div>
            </a>
            @endcan
            @can('view-payment-proof')
            <a href="{{ route('admin.payment-proofs.index') }}" class="flex items-center gap-3 p-3.5 border border-gray-100 bg-gray-50/50 rounded-xl hover:bg-green-50 hover:border-green-200 transition-all group">
                <div class="h-10 w-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-receipt text-green-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Verifikasi Bayar</p>
                    <p class="text-xs text-gray-400">Verifikasi bukti pembayaran</p>
                </div>
            </a>
            @endcan
            <a href="https://customer.andrinet.id" target="_blank" class="flex items-center gap-3 p-3.5 border border-gray-100 bg-gray-50/50 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-all group">
                <div class="h-10 w-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-external-link-alt text-blue-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Customer Portal</p>
                    <p class="text-xs text-gray-400">Buka portal pelanggan</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
