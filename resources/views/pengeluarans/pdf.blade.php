<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran - {{ \App\Models\CompanyProfile::first()->display_name ?? 'BCM' }} WiFi Billing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info strong {
            color: #374151;
        }
        .filters {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 14px;
        }
        .filters p {
            margin: 5px 0;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-lunas {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-belum-lunas {
            background-color: #fef3c7;
            color: #92400e;
        }
        .summary {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #374151;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            color: #6b7280;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $companyProfile = \App\Models\CompanyProfile::first();
            $companyName = $companyProfile->official_name ?? 'BCM';
            $companyInitials = $companyProfile->initials ?? 'BCM';
        @endphp
        <h1>{{ $companyName }}</h1>
        <p>{{ $companyInitials }} Laporan Data Pengeluaran</p>
        <p>Dicetak pada: {{ date('d F Y H:i:s') }}</p>
        @if($companyProfile)
            <p>{{ $companyProfile->alamat ?? '' }}</p>
            <p>Telp: {{ $companyProfile->nomor_kontak ?? '' }} | Email: {{ $companyProfile->email_support ?? '' }}</p>
        @endif
    </div>

    <div class="info">
        <strong>Informasi:</strong> Laporan ini berisi data pengeluaran operasional yang telah difilter sesuai kriteria yang dipilih.
    </div>

    @if($filters['search'] || $filters['kategori'] || $filters['status'] || $filters['metode_pembayaran'] || $filters['tahun'] || $filters['tanggal_dari'] || $filters['tanggal_sampai'])
    <div class="filters">
        <h3>Filter yang Diterapkan:</h3>
        @if($filters['search'])
            <p><strong>Pencarian:</strong> {{ $filters['search'] }}</p>
        @endif
        @if($filters['kategori'])
            <p><strong>Kategori:</strong> {{ $filters['kategori'] }}</p>
        @endif
        @if($filters['status'])
            <p><strong>Status:</strong> {{ $filters['status'] }}</p>
        @endif
        @if($filters['metode_pembayaran'])
            <p><strong>Metode Pembayaran:</strong> {{ $filters['metode_pembayaran'] }}</p>
        @endif
        @if($filters['tahun'])
            <p><strong>Tahun:</strong> {{ $filters['tahun'] }}</p>
        @endif
        @if($filters['tanggal_dari'])
            <p><strong>Tanggal Dari:</strong> {{ \Carbon\Carbon::parse($filters['tanggal_dari'])->format('d/m/Y') }}</p>
        @endif
        @if($filters['tanggal_sampai'])
            <p><strong>Tanggal Sampai:</strong> {{ \Carbon\Carbon::parse($filters['tanggal_sampai'])->format('d/m/Y') }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nama Pengeluaran</th>
                <th>Deskripsi</th>
                <th class="text-right">Jumlah</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluarans as $index => $pengeluaran)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('d/m/Y') }}</td>
                <td>{{ $pengeluaran->kategori }}</td>
                <td>{{ $pengeluaran->nama_pengeluaran }}</td>
                <td>{{ $pengeluaran->deskripsi }}</td>
                <td class="text-right">Rp {{ number_format((float)$pengeluaran->jumlah, 0, ',', '.') }}</td>
                <td>{{ $pengeluaran->metode_pembayaran }}</td>
                <td class="text-center">
                    <span class="status status-{{ $pengeluaran->status == 'lunas' ? 'lunas' : 'belum-lunas' }}">
                        {{ ucfirst($pengeluaran->status) }}
                    </span>
                </td>
                <td>{{ $pengeluaran->user->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data pengeluaran</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($pengeluarans->count() > 0)
    <div class="summary">
        <h3>Ringkasan</h3>
        <div class="summary-item">Total Pengeluaran: {{ $pengeluarans->count() }}</div>
        <div class="summary-item">Total Nilai: Rp {{ number_format($pengeluarans->sum('jumlah'), 0, ',', '.') }}</div>
        <div class="summary-item">Rata-rata: Rp {{ number_format($pengeluarans->avg('jumlah'), 0, ',', '.') }}</div>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }} | {{ date('d F Y H:i:s') }}</p>
        <p>{{ $companyName }} ({{ $companyInitials }}) WiFi Billing Management System</p>
    </div>
</body>
</html>
