@extends('layouts.app')

@section('title', 'Dashboard - WiFi Billing Management')

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $companyProfile = \App\Models\CompanyProfile::first();
    $companyName = $companyProfile->display_name ?? $companyProfile->nama_perusahaan ?? 'BCM';
@endphp

<div class="space-y-6 lg:space-y-7">
    <!-- Welcome Header Banner -->
    <div class="relative overflow-hidden rounded-2xl p-5 sm:p-7 shadow-sm text-white"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #1e40af 100%);">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/15 text-xs font-medium text-blue-200 mb-3">
                    <i class="far fa-calendar-alt text-[11px]"></i>
                    <span>{{ $greeting }}</span>
                    <span class="w-1 h-1 rounded-full bg-blue-300"></span>
                    <span>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-1.5">
                    Selamat Datang, {{ auth()->user()?->name ?? 'Admin' }}
                </h1>
                <p class="text-sm text-blue-200/90 font-normal">
                    {{ $companyName }} &bull; WiFi Customer & Billing Management System
                </p>
            </div>

            <!-- Server Time & Clear Cache Widget -->
            <div class="flex flex-col sm:flex-row gap-2.5 w-full lg:w-auto">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-xl px-4 py-3 min-w-[200px]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-500/30 text-blue-200 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-200">Server Time (WIB)</p>
                            <p class="text-base font-bold text-white font-mono tracking-tight" id="server-time">
                                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('dashboard.clear-cache') }}" method="POST" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit"
                            title="Segarkan cache dashboard"
                            class="w-full h-full bg-white/10 hover:bg-white/20 active:scale-[0.98] backdrop-blur-md border border-white/15 rounded-xl px-4 py-3 transition-all duration-200 text-left flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-lg bg-amber-500/30 text-amber-300 flex items-center justify-center flex-shrink-0 group-hover:rotate-180 transition-transform duration-500">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Clear Cache</p>
                            <p class="text-[11px] text-blue-200">Segarkan data</p>
                        </div>
                    </button>
                </form>
            </div>
        </div>

        <!-- Decorative background elements -->
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 right-32 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 lg:gap-4">
        @php
            $statCards = [
                [
                    'label' => 'Total Pelanggan',
                    'value' => number_format($stats['total_pelanggan']),
                    'trend' => $stats['customer_growth'],
                    'suffix' => ($stats['last_month_customers'] ?? 0) > 0 ? 'vs bulan lalu' : 'Bulan ini',
                    'icon' => 'fas fa-users',
                    'accent' => 'blue',
                    'bg_icon' => 'bg-blue-50',
                    'text_icon' => 'text-blue-600',
                ],
                [
                    'label' => 'Pendapatan Bulan Ini',
                    'value' => 'Rp ' . number_format($stats['pendapatan_bulan_ini'], 0, ',', '.'),
                    'trend' => $stats['revenue_growth'],
                    'suffix' => ($stats['last_month_revenue'] ?? 0) > 0 ? 'vs bulan lalu' : 'Bulan ini',
                    'icon' => 'fas fa-wallet',
                    'accent' => 'green',
                    'bg_icon' => 'bg-emerald-50',
                    'text_icon' => 'text-emerald-600',
                ],
                [
                    'label' => 'Tagihan Belum Lunas',
                    'value' => 'Rp ' . number_format($stats['tagihan_belum_bayar'], 0, ',', '.'),
                    'trend' => null,
                    'suffix' => 'Outstanding tagihan',
                    'icon' => 'fas fa-exclamation-circle',
                    'accent' => 'orange',
                    'bg_icon' => 'bg-amber-50',
                    'text_icon' => 'text-amber-600',
                ],
                [
                    'label' => 'Paket Aktif',
                    'value' => number_format($stats['total_paket']),
                    'trend' => null,
                    'suffix' => 'Paket layanan WiFi',
                    'icon' => 'fas fa-box-open',
                    'accent' => 'purple',
                    'bg_icon' => 'bg-purple-50',
                    'text_icon' => 'text-purple-600',
                ],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">
                            {{ $card['label'] }}
                        </p>
                        <p class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight mb-2">
                            {{ $card['value'] }}
                        </p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($card['trend'] === 'new')
                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold">Baru</span>
                            @elseif(is_numeric($card['trend']) && $card['trend'] > 0)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold inline-flex items-center gap-0.5">
                                    <i class="fas fa-arrow-up text-[8px]"></i>+{{ $card['trend'] }}%
                                </span>
                            @elseif(is_numeric($card['trend']) && $card['trend'] < 0)
                                <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-bold inline-flex items-center gap-0.5">
                                    <i class="fas fa-arrow-down text-[8px]"></i>{{ $card['trend'] }}%
                                </span>
                            @elseif($card['trend'] !== null)
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-semibold">0%</span>
                            @endif
                            <span class="text-[11px] text-gray-400 truncate">{{ $card['suffix'] }}</span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl {{ $card['bg_icon'] }} {{ $card['text_icon'] }} flex items-center justify-center flex-shrink-0 shadow-sm text-base">
                        <i class="{{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Pembayarans & Status Per Penagih -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Recent Pembayarans -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-receipt text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Pembayaran Terbaru</h3>
                        <p class="text-[11px] text-gray-400">Transaksi pembayaran terkini</p>
                    </div>
                </div>
                <a href="{{ route('pembayarans.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-gray-50 flex-1 p-2">
                @forelse($recentPembayarans as $pembayaran)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50/80 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">
                            {{ strtoupper(substr($pembayaran->pelanggan->nama ?? 'P', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $pembayaran->pelanggan->nama ?? '-' }}</p>
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p class="text-xs text-gray-400 truncate flex items-center gap-1.5">
                                    <span><i class="far fa-calendar-alt text-[10px] mr-1"></i>{{ $pembayaran->bulan_tagihan }}/{{ $pembayaran->tahun_tagihan }}</span>
                                    <span>&bull;</span>
                                    <span><i class="far fa-user text-[10px] mr-1"></i>{{ $pembayaran->penagih->nama ?? 'Tanpa Penagih' }}</span>
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold flex-shrink-0
                                    {{ $pembayaran->status === 'lunas' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    <i class="fas {{ $pembayaran->status === 'lunas' ? 'fa-check-circle' : 'fa-clock' }} mr-1 text-[9px]"></i>
                                    {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
                            <i class="fas fa-receipt text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Belum ada transaksi pembayaran</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Status Per Penagih -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-user-tie text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Status Per Penagih</h3>
                        <p class="text-[11px] text-gray-400">Performa & tagihan kolektor</p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-50 flex-1 p-2 space-y-1">
                @forelse($statusPerPenagih as $penagih)
                    <div class="p-3 rounded-xl hover:bg-gray-50/80 transition-colors">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($penagih['nama'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $penagih['nama'] }}</p>
                                    <p class="text-[11px] text-gray-400">
                                        <i class="fas fa-users text-[10px] mr-1"></i>{{ $penagih['total_pelanggan'] }} Pelanggan
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-rose-600">Rp {{ number_format($penagih['tagihan_belum_bayar'], 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400">Belum dibayar</p>
                            </div>
                        </div>

                        @php
                            $paid = max(0, $penagih['total_tagihan'] - $penagih['tagihan_belum_bayar']);
                            $percent = $penagih['total_tagihan'] > 0 ? round(($paid / $penagih['total_tagihan']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $percent >= 70 ? 'bg-emerald-500' : ($percent >= 40 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                     style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-gray-400 mt-1">
                                <span class="font-semibold {{ $percent >= 70 ? 'text-emerald-600' : ($percent >= 40 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $percent }}% Lunas
                                </span>
                                <span>Total: Rp {{ number_format($penagih['total_tagihan'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Data penagih belum tersedia</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    @if((auth()->user()?->role ?? 'guest') === 'admin')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="mb-4">
                <p class="text-[11px] uppercase tracking-widest text-purple-600 font-bold mb-0.5">Admin Utilities</p>
                <h3 class="text-sm font-bold text-gray-900">Aksi & Shortcut Billing</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ route('backup.database') }}"
                   class="border border-gray-100 bg-gray-50/50 rounded-xl p-4 hover:bg-blue-50/60 hover:border-blue-200 transition-all group">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-200 transition-colors flex-shrink-0">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Backup Database</p>
                            <p class="text-xs text-gray-500 mt-0.5">Download cadangan data terkini</p>
                            <span class="inline-block mt-1 text-[10px] font-bold text-blue-600 uppercase tracking-wider">Keamanan Data</span>
                        </div>
                    </div>
                </a>

                <form method="POST" action="{{ route('pembayarans.generate-bills') }}" class="generate-bills-form">
                    @csrf
                    <button type="submit"
                            class="w-full text-left border border-gray-100 bg-gray-50/50 rounded-xl p-4 hover:bg-emerald-50/60 hover:border-emerald-200 transition-all group generate-bills-btn">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-200 transition-colors flex-shrink-0">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Generate Tagihan</p>
                                <p class="text-xs text-gray-500 mt-0.5">Buat tagihan bulanan pelanggan</p>
                                <span class="inline-block mt-1 text-[10px] font-bold text-emerald-600 uppercase tracking-wider">1x Per Bulan</span>
                            </div>
                        </div>
                    </button>
                </form>

                <form method="POST" action="{{ route('run.smart.bills') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left border border-gray-100 bg-gray-50/50 rounded-xl p-4 hover:bg-purple-50/60 hover:border-purple-200 transition-all group smart-bills-btn">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-purple-200 transition-colors flex-shrink-0">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Run Smart Bills</p>
                                <p class="text-xs text-gray-500 mt-0.5">Generate khusus pelanggan baru</p>
                                <span class="inline-block mt-1 text-[10px] font-bold text-purple-600 uppercase tracking-wider">Otomatis Tambahan</span>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    // Real-time clock function
    function updateTime() {
        const timeElement = document.getElementById('server-time');
        if (!timeElement) return;

        const now = new Date();
        const timeString = now.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta'
        });

        timeElement.textContent = timeString;
    }

    updateTime();
    setInterval(updateTime, 1000);

    // Handle Smart Bills button with SweetAlert
    const smartBillsBtn = document.querySelector('.smart-bills-btn');
    if (smartBillsBtn) {
        smartBillsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Jalankan Smart Bills?',
                text: 'Apakah Anda yakin ingin menjalankan Smart Bills Check untuk pelanggan baru?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Jalankan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    }

    const generateBillsBtn = document.querySelector('.generate-bills-btn');
    if (generateBillsBtn) {
        generateBillsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Generate Tagihan Bulanan?',
                text: 'Tindakan ini hanya bisa dilakukan satu kali per bulan. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Generate',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    }
</script>

@if(session('success'))
<script>
Swal.fire({
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    icon: 'success',
    confirmButtonColor: '#10B981'
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    title: 'Error!',
    text: '{{ session('error') }}',
    icon: 'error',
    confirmButtonColor: '#EF4444'
});
</script>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection

