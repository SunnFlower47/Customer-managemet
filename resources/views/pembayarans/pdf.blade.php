ny<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran - {{ \App\Models\CompanyProfile::first()->display_name ?? 'BCM' }} WiFi Billing</title>
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
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #2563eb;
        }
        .filters {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 8px;
            padding: 6px 12px;
            background-color: #e3f2fd;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 10px;
        }
        .status-lunas {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-belum {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            font-weight: bold;
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
        <p>{{ $companyInitials }} Laporan Data Pembayaran</p>
        <p>Dicetak pada: {{ date('d F Y H:i:s') }}</p>
        @if($companyProfile)
            <p>{{ $companyProfile->alamat ?? '' }}</p>
            <p>Telp: {{ $companyProfile->nomor_kontak ?? '' }} | Email: {{ $companyProfile->email_support ?? '' }}</p>
        @endif
    </div>

    <div class="info">
        <strong>Informasi:</strong> Laporan ini berisi data pembayaran pelanggan WiFi yang telah difilter sesuai kriteria yang dipilih.
    </div>

    @if($filters['status'] || $filters['bulan'] || $filters['tahun'] || $filters['penagih_id'])
    <div class="filters">
        <strong>Filter yang Diterapkan:</strong><br><br>
        @if($filters['status'])
            <span class="filter-item"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</span>
        @endif
        @if($filters['bulan'])
            <span class="filter-item"><strong>Bulan:</strong> {{ \Carbon\Carbon::create(null, $filters['bulan'], 1)->format('F') }}</span>
        @endif
        @if($filters['tahun'])
            <span class="filter-item"><strong>Tahun:</strong> {{ $filters['tahun'] }}</span>
        @endif
        @if($filters['penagih_id'])
            @php
                $penagih = \App\Models\Penagih::find($filters['penagih_id']);
            @endphp
            <span class="filter-item"><strong>Penagih:</strong> {{ $penagih ? $penagih->nama : 'Unknown' }}</span>
        @endif
        <br><br>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>PPPoE</th>
                <th>Penagih</th>
                <th>Periode</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayarans as $index => $pembayaran)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pembayaran->pelanggan->nama }}</td>
                <td>{{ $pembayaran->pelanggan->pppoe }}</td>
                <td>
                    @if($pembayaran->penagih)
                        {{ $pembayaran->penagih->nama }}
                    @else
                        <span class="text-gray-400 italic">Belum ada penagih</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') }} {{ $pembayaran->tahun_tagihan }}</td>
                <td>Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</td>
                <td>
                    @if($pembayaran->status === 'lunas')
                        <span class="status-lunas">Lunas</span>
                    @else
                        <span class="status-belum">Belum Bayar</span>
                    @endif
                </td>
                <td>
                    @if($pembayaran->tanggal_bayar)
                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                    Tidak ada data pembayaran yang ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($pembayarans->count() > 0)
    <div class="summary">
        <div class="summary-item">Total Data: {{ $pembayarans->count() }}</div>
        <div class="summary-item">Total Lunas: {{ $pembayarans->where('status', 'lunas')->count() }}</div>
        <div class="summary-item">Total Belum Bayar: {{ $pembayarans->where('status', 'belum_bayar')->count() }}</div>
        <div class="summary-item">Total Nilai: Rp {{ number_format($pembayarans->sum('jumlah'), 0, ',', '.') }}</div>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }} | {{ date('d F Y H:i:s') }}</p>
        <p>{{ $companyName }} ({{ $companyInitials }}) WiFi Billing Management System</p>
    </div>
</body>
</html>
