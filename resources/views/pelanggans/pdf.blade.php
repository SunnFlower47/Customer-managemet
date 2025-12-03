<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - WiFi Billing Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
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
            padding: 10px;
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
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
            vertical-align: top;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        tr.even {
            background-color: #f9f9f9;
        }
        .status-aktif {
            color: #059669;
            font-weight: bold;
        }
        .status-isolir {
            color: #dc2626;
            font-weight: bold;
        }
        .status-bayar-double {
            color: #d97706;
            font-weight: bold;
        }
        .status-nonaktif {
            color: #6b7280;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $companyProfile = \App\Models\CompanyProfile::first();
        @endphp
        <h1>{{ $companyProfile->official_name ?? 'BCM' }}</h1>
        <p>Data Pelanggan</p>
        <p>Dicetak pada: {{ date('d M Y H:i:s') }}</p>
        @if($companyProfile)
            <p>{{ $companyProfile->alamat ?? '' }}</p>
            <p>Telp: {{ $companyProfile->nomor_kontak ?? '' }} | Email: {{ $companyProfile->email_support ?? '' }}</p>
        @endif
    </div>

    <div class="info">
        <strong>Total Pelanggan:</strong> {{ $pelanggans->count() }} orang<br>
        <strong>Pelanggan Aktif:</strong> {{ $pelanggans->where('status', 'aktif')->count() }} orang<br>
        <strong>Pelanggan Isolir:</strong> {{ $pelanggans->where('status', 'isolir')->count() }} orang<br>
        <strong>Pelanggan Bayar Double:</strong> {{ $pelanggans->where('status', 'bayar double')->count() }} orang<br>
        <strong>Pelanggan Nonaktif:</strong> {{ $pelanggans->where('status', 'nonaktif')->count() }} orang
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Pelanggan</th>
                <th style="width: 12%;">PPPoE</th>
                <th style="width: 12%;">No. HP</th>
                <th style="width: 15%;">Paket</th>
                <th style="width: 12%;">Penagih</th>
                <th style="width: 8%;">Tgl Bayar</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 8%;">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelanggans as $index => $pelanggan)
            <tr class="{{ $index % 2 == 1 ? 'even' : '' }}">
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $pelanggan->nama }}</strong></td>
                <td>{{ $pelanggan->pppoe }}</td>
                <td>{{ $pelanggan->no_hp }}</td>
                <td>
                    @if($pelanggan->paket)
                        {{ $pelanggan->paket->nama_paket }}<br>
                        <small>Rp {{ number_format((float)$pelanggan->paket->harga, 0, ',', '.') }}</small>
                    @else
                        <span class="text-gray-400 italic">Belum ada paket</span>
                    @endif
                </td>
                <td>
                    @if($pelanggan->penagih)
                        {{ $pelanggan->penagih->nama }}
                    @else
                        <span class="text-gray-400 italic">Belum ada penagih</span>
                    @endif
                </td>
                <td>Tanggal {{ $pelanggan->tanggal_pembayaran }}</td>
                <td class="status-{{ str_replace(' ', '-', $pelanggan->status) }}">
                    {{ ucfirst($pelanggan->status) }}
                </td>
                <td>{{ \Illuminate\Support\Str::limit($pelanggan->alamat, 50) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">
                    Tidak ada data pelanggan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem WiFi Customer Management</p>
        <p>Untuk informasi lebih lanjut, hubungi administrator sistem</p>
    </div>

</body>
</html>
