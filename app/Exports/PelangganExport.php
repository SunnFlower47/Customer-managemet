<?php

namespace App\Exports;

use App\Models\Pelanggan;
use App\Models\CompanyProfile;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PelangganExport implements FromArray, WithStyles, WithTitle
{
    protected $pelanggans;
    protected $companyProfile;

    public function __construct($pelanggans)
    {
        $this->pelanggans = $pelanggans;
        $this->companyProfile = CompanyProfile::first();
    }

    public function title(): string
    {
        return 'Daftar Pelanggan';
    }

    public function array(): array
    {
        $company = $this->companyProfile;
        $ppnLabel = 'PPN ' . ($company->ppn_persen ?? 11) . '%';
        $bhpLabel = 'BHP ' . ($company->bhp_persen ?? 0.5) . '%';
        $usoLabel = 'USO ' . ($company->uso_persen ?? 1.25) . '%';
        $admLabel = 'ADM 2,25%'; // sesuai gambar

        $rows = [];

        // Header perusahaan
        $rows[] = [$company->official_name ?? $company->nama_perusahaan ?? 'PT. PERUSAHAAN', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['LAPORAN DAFTAR PELANGGAN', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['NAMA MITRA', ':', '', '', '', '', '', '', '', ''];
        $rows[] = ['ALAMAT', ':', '', '', '', '', '', '', '', ''];
        $rows[] = ['TIKOR', ':', '', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

        // Header kolom (kuning bold)
        $rows[] = ['NO', 'NAMA PELANGGAN', 'ALAMAT', 'NIK', 'HARGA PAKET', $ppnLabel, $bhpLabel, $usoLabel, $admLabel, 'TOTAL'];

        // Data pelanggan
        $no = 1;
        foreach ($this->pelanggans as $pelanggan) {
            $hargaDasar = $pelanggan->paket ? (float)$pelanggan->paket->harga_dasar : 0;
            $ppnNominal = $pelanggan->paket ? (float)$pelanggan->paket->ppn_nominal : 0;
            $bhpNominal = $pelanggan->paket ? (float)$pelanggan->paket->bhp_nominal : 0;
            $usoNominal = $pelanggan->paket ? (float)$pelanggan->paket->uso_nominal : 0;
            $admNominal = $hargaDasar > 0 ? round($hargaDasar * 0.0225) : 0;
            $total = $hargaDasar + $ppnNominal + $bhpNominal + $usoNominal + $admNominal;

            $rows[] = [
                $no++,
                $pelanggan->nama,
                $pelanggan->alamat,
                $pelanggan->nik ?? '-',
                $hargaDasar > 0 ? $hargaDasar : '-',
                $ppnNominal > 0 ? $ppnNominal : '-',
                $bhpNominal > 0 ? $bhpNominal : '-',
                $usoNominal > 0 ? $usoNominal : '-',
                $admNominal > 0 ? $admNominal : '-',
                $total > 0 ? $total : '-',
            ];
        }

        // Baris kosong di akhir
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = count($this->pelanggans) + 9; // 7 header + 1 kolom + data + 1 kosong

        // Row 1: Nama perusahaan - bold besar
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        // Row 2: LAPORAN - bold
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        // Baris 4-6: NAMA MITRA, ALAMAT, TIKOR
        foreach ([4, 5, 6] as $row) {
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        }

        // Header Kolom (Row 8) - background kuning, bold, border
        $headerRow = 8;
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")
            ->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")
            ->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFFF00');
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data rows: border tipis, angka rata kanan
        $dataStart = $headerRow + 1;
        $dataEnd = $dataStart + count($this->pelanggans) - 1;

        if ($dataEnd >= $dataStart) {
            $sheet->getStyle("A{$dataStart}:J{$dataEnd}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Kolom angka rata kanan
            foreach (['E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                // Format angka ribuan
                $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")
                    ->getNumberFormat()->setFormatCode('#,##0;-;-');
            }

            // Nomor (kolom A) - center
            $sheet->getStyle("A{$dataStart}:A{$dataEnd}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15);

        // Tinggi header row
        $sheet->getRowDimension($headerRow)->setRowHeight(22);
    }
}
