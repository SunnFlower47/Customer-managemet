<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Pelanggan</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 9px; color: #000; }
.hdr { text-align: center; margin-bottom: 8px; }
.hdr h1 { font-size: 13px; font-weight: bold; }
.hdr p { font-size: 9px; }
.info { margin-bottom: 6px; font-size: 9px; }
table { width: 100%; border-collapse: collapse; }
th { background: #1e40af; color: #fff; font-size: 8px; font-weight: bold; padding: 4px 3px; border: 1px solid #000; text-align: center; }
td { border: 1px solid #ccc; padding: 3px; font-size: 8px; vertical-align: top; }
tr:nth-child(even) td { background: #f5f5f5; }
.foot { margin-top: 8px; font-size: 8px; text-align: center; color: #555; }
</style>
</head>
<body>
@php
    $cp = \App\Models\CompanyProfile::first();
@endphp
<div class="hdr">
    <h1>{{ $cp->official_name ?? $cp->nama_perusahaan ?? 'WIFI BILLING' }}</h1>
    <p>Laporan Daftar Pelanggan &mdash; Dicetak: {{ date('d/m/Y H:i') }}</p>
</div>

<div class="info">
    Total: <b>{{ $pelanggans->count() }}</b> pelanggan &nbsp;|&nbsp;
    Aktif: <b>{{ $pelanggans->where('status','aktif')->count() }}</b> &nbsp;|&nbsp;
    Isolir: <b>{{ $pelanggans->where('status','isolir')->count() }}</b> &nbsp;|&nbsp;
    Nonaktif: <b>{{ $pelanggans->where('status','nonaktif')->count() }}</b>
</div>

<table>
    <thead>
        <tr>
            <th style="width:3%">No</th>
            <th style="width:16%">Nama</th>
            <th style="width:10%">NIK</th>
            <th style="width:10%">PPPoE</th>
            <th style="width:9%">No HP</th>
            <th style="width:14%">Paket</th>
            <th style="width:10%">Harga Dasar</th>
            <th style="width:8%">Total</th>
            <th style="width:9%">Penagih</th>
            <th style="width:5%">Tgl</th>
            <th style="width:6%">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pelanggans as $i => $p)
        <tr>
            <td style="text-align:center">{{ $i + 1 }}</td>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->nik ?: '-' }}</td>
            <td>{{ $p->pppoe }}</td>
            <td>{{ $p->no_hp }}</td>
            <td>{{ $p->paket ? $p->paket->nama_paket : '-' }}</td>
            <td style="text-align:right">{{ $p->paket && $p->paket->harga_dasar ? number_format((float)$p->paket->harga_dasar,0,',','.') : '-' }}</td>
            <td style="text-align:right">{{ $p->paket ? number_format((float)$p->paket->harga,0,',','.') : '-' }}</td>
            <td>{{ $p->penagih ? $p->penagih->nama : '-' }}</td>
            <td style="text-align:center">{{ $p->tanggal_pembayaran }}</td>
            <td style="text-align:center">{{ ucfirst($p->status) }}</td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;padding:10px">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="foot">Dicetak otomatis oleh sistem &mdash; {{ date('d M Y H:i:s') }}</div>
</body>
</html>
