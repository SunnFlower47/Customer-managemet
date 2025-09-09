<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $pembayarans;

    public function __construct($pembayarans)
    {
        $this->pembayarans = $pembayarans;
    }

    public function collection()
    {
        return $this->pembayarans;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pelanggan',
            'PPPoE',
            'No HP',
            'Alamat',
            'Nama Penagih',
            'Bulan Tagihan',
            'Tahun Tagihan',
            'Jumlah',
            'Status',
            'Tanggal Bayar',
            'Keterangan'
        ];
    }

    public function map($pembayaran): array
    {
        return [
            $pembayaran->id,
            $pembayaran->pelanggan->nama,
            $pembayaran->pelanggan->pppoe,
            $pembayaran->pelanggan->no_hp,
            $pembayaran->pelanggan->alamat,
            $pembayaran->penagih->nama,
            \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('F'),
            $pembayaran->tahun_tagihan,
            $pembayaran->jumlah,
            $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Bayar',
            $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i') : '-',
            $pembayaran->keterangan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 20,  // Nama Pelanggan
            'C' => 15,  // PPPoE
            'D' => 15,  // No HP
            'E' => 30,  // Alamat
            'F' => 20,  // Nama Penagih
            'G' => 15,  // Bulan Tagihan
            'H' => 12,  // Tahun Tagihan
            'I' => 15,  // Jumlah
            'J' => 12,  // Status
            'K' => 18,  // Tanggal Bayar
            'L' => 20,  // Keterangan
        ];
    }
}
