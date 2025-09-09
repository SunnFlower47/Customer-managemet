@extends('layouts.app')

@section('title', 'Dashboard - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang, {{ auth()->user()?->name ?? 'Guest' }}!</h1>
                <p class="text-gray-600">Dashboard {{ \App\Models\CompanyProfile::first()->initials ?? 'BCM' }} WiFi Customer Management System - {{ \App\Models\CompanyProfile::first()->display_name ?? 'BCM' }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500 flex items-center">
                                <span>Server Time</span>
                                <span id="sync-indicator" class="ml-2 text-xs text-green-500 hidden">
                                    <i class="fas fa-sync-alt animate-spin"></i> Synced
                                </span>
                            </div>
                            <div class="text-lg font-semibold text-gray-900" id="server-time">
                                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}
                            </div>
                            <div class="text-xs text-gray-400">WIB (GMT+7)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Pelanggan</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_pelanggan']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pendapatan Bulan Ini</dt>
                        <dd class="text-xl font-semibold text-gray-900">Rp {{ number_format($stats['pendapatan_bulan_ini'], 0, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Tagihan Belum Bayar</dt>
                        <dd class="text-xl font-semibold text-gray-900">Rp {{ number_format($stats['tagihan_belum_bayar'], 0, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Paket</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_paket']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Pembayarans & Status Per Penagih -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        <!-- Recent Pembayarans -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-receipt mr-2 text-blue-600"></i>Pembayaran Terbaru
                </h3>
            </div>
            <div class="p-6">
                <div class="flow-root">
                    <ul class="-my-4 divide-y divide-gray-200">
                        @forelse($recentPembayarans as $pembayaran)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-gray-600">
                                            {{ substr($pembayaran->pelanggan->nama, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $pembayaran->pelanggan->nama }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        @if($pembayaran->penagih)
                                            {{ $pembayaran->penagih->nama }}
                                        @else
                                            <span class="text-gray-400 italic">Belum ada penagih</span>
                                        @endif
                                        • {{ $pembayaran->bulan_tagihan }}/{{ $pembayaran->tahun_tagihan }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pembayaran->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}
                                    </span>
                                    <div class="text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-gray-300 text-3xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada pembayaran</h3>
                                <p class="text-xs text-gray-500">Belum ada data pembayaran terbaru.</p>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Status Per Penagih -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-tie mr-2 text-green-600"></i>Status Per Penagih
                </h3>
            </div>
            <div class="p-6">
                <div class="flow-root">
                    <ul class="-my-4 divide-y divide-gray-200">
                        @forelse($statusPerPenagih as $penagih)
                        <li class="py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-gray-600">
                                                {{ substr($penagih['nama'], 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $penagih['nama'] }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $penagih['total_pelanggan'] }} pelanggan
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($penagih['tagihan_belum_bayar'], 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-red-600 font-medium">Belum bayar</p>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-tie text-gray-300 text-3xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada penagih</h3>
                                <p class="text-xs text-gray-500">Belum ada data penagih.</p>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    @if((auth()->user()?->role ?? 'guest') === 'admin')
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-cogs mr-2 text-purple-600"></i>Admin Actions
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('backup.database') }}" class="group bg-white border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-database text-blue-600 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-900">Backup Database</h4>
                            <p class="text-xs text-gray-500">Backup data sistem</p>
                        </div>
                    </div>
                </a>

                <form method="POST" action="{{ route('pembayarans.generate-bills') }}" class="group">
                    @csrf
                    <button type="submit" class="w-full bg-white border border-gray-200 rounded-lg p-4 hover:border-green-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-green-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-semibold text-gray-900">Generate Tagihan</h4>
                                <p class="text-xs text-gray-500">Buat tagihan bulanan</p>
                            </div>
                        </div>
                    </button>
                </form>

                <form method="POST" action="{{ route('run.smart.bills') }}" class="group">
                    @csrf
                    <button type="submit" class="w-full bg-white border border-gray-200 rounded-lg p-4 hover:border-purple-300 hover:shadow-md transition-all duration-200 smart-bills-btn">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-brain text-purple-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-semibold text-gray-900">Run Smart Bills</h4>
                                <p class="text-xs text-gray-500">Cek tagihan otomatis</p>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
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

    // Show SweetAlert for session messages
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#10B981'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#EF4444'
        });
    @endif
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection
