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
.num { text-align: right; }
.ctr { text-align: center; }
.foot { margin-top: 8px; font-size: 8px; text-align: center; color: #555; }
</style>
</head>
<body>
@php
    $cp  = \App\Models\CompanyProfile::first();
    $ppn = $cp->ppn_persen ?? 11;
    $bhp = $cp->bhp_persen ?? 0.5;
    $uso = $cp->uso_persen ?? 1.25;
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
            <th style="width:18%">Nama</th>
            <th style="width:11%">NIK</th>
            <th style="width:9%">No HP</th>
            <th style="width:14%">Nama Paket</th>
            <th style="width:10%">Harga Dasar</th>
            <th style="width:9%">PPN {{ $ppn }}%</th>
            <th style="width:9%">BHP {{ $bhp }}%</th>
            <th style="width:9%">USO {{ $uso }}%</th>
            <th style="width:8%">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pelanggans as $i => $p)
        @php
            $hargaDasar = $p->paket ? (float)$p->paket->harga_dasar : 0;
            $ppnNom     = $p->paket ? (float)$p->paket->ppn_nominal  : 0;
            $bhpNom     = $p->paket ? (float)$p->paket->bhp_nominal  : 0;
            $usoNom     = $p->paket ? (float)$p->paket->uso_nominal  : 0;
            $total      = $hargaDasar + $ppnNom + $bhpNom + $usoNom;
        @endphp
        <tr>
            <td class="ctr">{{ $i + 1 }}</td>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->nik ?: '-' }}</td>
            <td>{{ $p->no_hp }}</td>
            <td>{{ $p->paket ? $p->paket->nama_paket : '-' }}</td>
            <td class="num">{{ $hargaDasar > 0 ? number_format($hargaDasar, 0, ',', '.') : '-' }}</td>
            <td class="num">{{ $ppnNom > 0 ? number_format($ppnNom, 0, ',', '.') : '-' }}</td>
            <td class="num">{{ $bhpNom > 0 ? number_format($bhpNom, 0, ',', '.') : '-' }}</td>
            <td class="num">{{ $usoNom > 0 ? number_format($usoNom, 0, ',', '.') : '-' }}</td>
            <td class="num"><b>{{ $total > 0 ? number_format($total, 0, ',', '.') : '-' }}</b></td>
        </tr>
        @empty
        <tr><td colspan="10" class="ctr" style="padding:10px">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="foot">Dicetak otomatis oleh sistem &mdash; {{ date('d M Y H:i:s') }}</div>
</body>
</html>
