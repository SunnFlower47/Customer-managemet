@extends('layouts.app')

@section('title', 'Dashboard - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Welcome Section -->
    <div class="app-card app-card--soft">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-gray-400 mb-2">Dashboard Overview</p>
                <h1 class="page-header__title text-slate-900 mb-3">
                    Selamat Datang, {{ auth()->user()?->name ?? 'Guest' }}!
                </h1>
                <p class="text-sm sm:text-base text-gray-600">
                    {{ \App\Models\CompanyProfile::first()->display_name ?? 'BCM' }} WiFi Customer Management System
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="flex-1 sm:flex-[1.2] bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 tracking-wide uppercase">Server Time</p>
                            <p class="text-lg font-semibold text-gray-900" id="server-time">
                                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}
                            </p>
                            <p class="text-[11px] text-gray-400">WIB (GMT+7)</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('dashboard.clear-cache') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full h-full bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                <i class="fas fa-sync-alt text-amber-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Clear Cache</p>
                                <p class="text-xs text-gray-500">Segarkan data dashboard</p>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        @php
            $statCards = [
                [
                    'label' => 'Total Pelanggan',
                    'value' => number_format($stats['total_pelanggan']),
                    'trend' => $stats['customer_growth'],
                    'suffix' => $stats['last_month_customers'] ?? 0 > 0 ? 'vs bulan lalu' : 'Data baru bulan ini',
                    'icon' => 'fas fa-users',
                    'accent' => 'blue'
                ],
                [
                    'label' => 'Pendapatan Bulan Ini',
                    'value' => 'Rp ' . number_format($stats['pendapatan_bulan_ini'], 0, ',', '.'),
                    'trend' => $stats['revenue_growth'],
                    'suffix' => $stats['last_month_revenue'] ?? 0 > 0 ? 'vs bulan lalu' : 'Data baru bulan ini',
                    'icon' => 'fas fa-dollar-sign',
                    'accent' => 'green'
                ],
                [
                    'label' => 'Tagihan Belum Lunas',
                    'value' => 'Rp ' . number_format($stats['tagihan_belum_bayar'], 0, ',', '.'),
                    'trend' => null,
                    'suffix' => 'Perlu perhatian',
                    'icon' => 'fas fa-exclamation-triangle',
                    'accent' => 'orange'
                ],
                [
                    'label' => 'Paket Aktif',
                    'value' => number_format($stats['total_paket']),
                    'trend' => null,
                    'suffix' => 'Paket tersedia',
                    'icon' => 'fas fa-box',
                    'accent' => 'purple'
                ],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="app-card stat-card bg-white border border-gray-100 shadow-sm">
                <div class="flex items-start justify-between gap-2.5">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 mb-2">
                            {{ $card['label'] }}
                        </p>
                        <p class="stat-card__value mb-1.5 leading-tight">{{ $card['value'] }}</p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($card['trend'] === 'new')
                                <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-semibold">Baru</span>
                            @elseif(is_numeric($card['trend']) && $card['trend'] > 0)
                                <span class="px-1.5 py-0.5 rounded-full bg-green-50 text-green-600 text-[10px] font-semibold">
                                    <i class="fas fa-arrow-up mr-0.5"></i>+{{ $card['trend'] }}%
                                </span>
                            @elseif(is_numeric($card['trend']) && $card['trend'] < 0)
                                <span class="px-1.5 py-0.5 rounded-full bg-red-50 text-red-600 text-[10px] font-semibold">
                                    <i class="fas fa-arrow-down mr-0.5"></i>{{ $card['trend'] }}%
                                </span>
                            @elseif($card['trend'] !== null)
                                <span class="px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-semibold">0%</span>
                            @endif
                            <span class="text-[10px] text-gray-500 truncate">{{ $card['suffix'] }}</span>
                        </div>
                    </div>
                    <div class="stat-card__icon shadow-sm flex-shrink-0
                        @if($card['accent'] === 'blue') bg-blue-50 text-blue-600
                        @elseif($card['accent'] === 'green') bg-emerald-50 text-emerald-600
                        @elseif($card['accent'] === 'orange') bg-orange-50 text-orange-500
                        @else bg-purple-50 text-purple-600 @endif">
                        <i class="{{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Pembayarans & Status Per Penagih -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Recent Pembayarans -->
        <div class="app-card">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-receipt text-blue-500"></i>
                        Pembayaran Terbaru
                </h3>
                    <p class="text-xs text-gray-500 mt-1">5 transaksi terakhir</p>
                </div>
                <a href="{{ route('pembayarans.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-500">Lihat semua →</a>
            </div>
            <div class="space-y-3">
                        @forelse($recentPembayarans as $pembayaran)
                    <div class="flex items-start gap-3 rounded-xl border border-gray-100 p-3 hover:border-blue-200 transition">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                            {{ substr($pembayaran->pelanggan->nama, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $pembayaran->pelanggan->nama }}</p>
                                    <p class="text-xs text-gray-500 flex items-center gap-2">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-calendar"></i>{{ $pembayaran->bulan_tagihan }}/{{ $pembayaran->tahun_tagihan }}
                                        </span>
                                        <span class="hidden sm:inline">•</span>
                                        <span class="flex items-center gap-1">
                                        @if($pembayaran->penagih)
                                                <i class="fas fa-user"></i>{{ $pembayaran->penagih->nama }}
                                        @else
                                                <i class="fas fa-user-slash text-gray-400"></i>
                                                <span class="italic text-gray-400">Belum ada penagih</span>
                                        @endif
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right sm:flex-shrink-0">
                                    <p class="text-sm font-semibold text-slate-900">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                        {{ $pembayaran->status === 'lunas' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        <i class="fas {{ $pembayaran->status === 'lunas' ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                        {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum bayar' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                        @empty
                    <div class="text-center py-10 text-sm text-gray-500">
                        Belum ada transaksi terbaru.
                            </div>
                        @endforelse
            </div>
        </div>

        <!-- Status Per Penagih -->
        <div class="app-card">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-tie text-emerald-500"></i>
                        Status Per Penagih
                </h3>
                    <p class="text-xs text-gray-500 mt-1">Outstanding & performa kolektor</p>
                </div>
            </div>
            <div class="space-y-3">
                        @forelse($statusPerPenagih as $penagih)
                    <div class="border border-gray-100 rounded-xl p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-semibold">
                                                {{ substr($penagih['nama'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $penagih['nama'] }}</p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-users"></i>{{ $penagih['total_pelanggan'] }} pelanggan
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-rose-600">Rp {{ number_format($penagih['tagihan_belum_bayar'], 0, ',', '.') }}</p>
                                <p class="text-[11px] text-gray-500">Belum dibayar</p>
                            </div>
                        </div>
                        @php
                            $paid = max(0, $penagih['total_tagihan'] - $penagih['tagihan_belum_bayar']);
                            $percent = $penagih['total_tagihan'] > 0 ? round(($paid / $penagih['total_tagihan']) * 100) : 0;
                        @endphp
                        <div class="mt-3">
                            <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $percent >= 70 ? 'bg-emerald-400' : 'bg-amber-400' }}" style="width: {{ $percent }}%"></div>
                                </div>
                            <div class="flex justify-between text-[11px] text-gray-500 mt-1">
                                <span>{{ $percent }}% lunas</span>
                                <span>Total: Rp {{ number_format($penagih['total_tagihan'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-sm text-gray-500">Data penagih belum tersedia.</div>
                        @endforelse
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    @if((auth()->user()?->role ?? 'guest') === 'admin')
        <div class="app-card">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-cogs text-purple-500"></i>
                        Admin Actions
            </h3>
                    <p class="text-xs text-gray-500 mt-1">Shortcut utilitas utama</p>
                </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <a href="{{ route('backup.database') }}" class="border border-gray-100 rounded-xl p-4 hover:border-blue-200 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Backup Database</p>
                            <p class="text-xs text-gray-500 mt-1">Download cadangan terkini</p>
                            <span class="text-[11px] text-blue-600 font-semibold">Keamanan data</span>
                        </div>
                    </div>
                </a>
                <form method="POST" action="{{ route('pembayarans.generate-bills') }}" class="border border-gray-100 rounded-xl p-4 hover:border-emerald-200 transition generate-bills-form">
                    @csrf
                    <button type="submit" class="w-full text-left generate-bills-btn">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Generate Tagihan</p>
                                <p class="text-xs text-gray-500 mt-1">Buat tagihan bulanan (1x per periode)</p>
                                <span class="text-[11px] text-emerald-600 font-semibold">Sekali jalan tiap bulan</span>
                            </div>
                        </div>
                    </button>
                </form>
                <form method="POST" action="{{ route('run.smart.bills') }}" class="border border-gray-100 rounded-xl p-4 hover:border-purple-200 transition smart-bills-btn">
                    @csrf
                    <button type="submit" class="w-full text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Run Smart Bills</p>
                                <p class="text-xs text-gray-500 mt-1">Generate khusus pelanggan baru/belum masuk</p>
                                <span class="text-[11px] text-purple-600 font-semibold">Tambahan pascadistribusi</span>
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

    // Start clock immediately
    updateTime();
    setInterval(updateTime, 1000);

    // Handle Smart Bills button with SweetAlert
    const smartBillsBtn = document.querySelector('.smart-bills-btn');
    if (smartBillsBtn) {
        smartBillsBtn.addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Jalankan Smart Bills?',
                text: 'Apakah Anda yakin ingin menjalankan Smart Bills Check?',
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

    // Show SweetAlert for session messages
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
