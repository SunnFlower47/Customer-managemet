<?php

namespace App\Exports;

use App\Models\CompanyProfile;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PelangganExport implements FromQuery, WithMapping, WithTitle, WithChunkReading, WithEvents
{
    protected $query;
    protected $companyProfile;
    protected $rowNumber = 0;

    // Row offset karena ada 7 baris header sebelum data (baris 1-7)
    // Baris 8 = header kolom, baris 9+ = data
    const HEADER_OFFSET = 8;

    public function __construct($query)
    {
        $this->query    = $query;
        $this->companyProfile = CompanyProfile::first();
    }

    public function title(): string
    {
        return 'Daftar Pelanggan';
    }

    public function chunkSize(): int
    {
        return 200; // proses 200 baris per chunk
    }

    public function query()
    {
        return $this->query;
    }

    public function map($pelanggan): array
    {
        $this->rowNumber++;

        $hargaDasar = $pelanggan->paket ? (float)$pelanggan->paket->harga_dasar : 0;
        $ppnNominal = $pelanggan->paket ? (float)$pelanggan->paket->ppn_nominal : 0;
        $bhpNominal = $pelanggan->paket ? (float)$pelanggan->paket->bhp_nominal : 0;
        $usoNominal = $pelanggan->paket ? (float)$pelanggan->paket->uso_nominal : 0;
        $admNominal = $pelanggan->paket ? (float)($pelanggan->paket->adm_nominal ?? 0) : 0;
        $total      = $hargaDasar + $ppnNominal + $bhpNominal + $usoNominal + $admNominal;

        return [
            $this->rowNumber,
            $pelanggan->nama,
            $pelanggan->alamat,
            $pelanggan->nik ?? '-',
            $hargaDasar  > 0 ? $hargaDasar  : '-',
            $ppnNominal  > 0 ? $ppnNominal  : '-',
            $bhpNominal  > 0 ? $bhpNominal  : '-',
            $usoNominal  > 0 ? $usoNominal  : '-',
            $admNominal  > 0 ? $admNominal  : '-',
            $total       > 0 ? $total        : '-',
        ];
    }

    public function registerEvents(): array
    {
        $company    = $this->companyProfile;
        $ppnLabel   = 'PPN ' . ($company->ppn_persen ?? 11) . '%';
        $bhpLabel   = 'BHP ' . ($company->bhp_persen ?? 0.5) . '%';
        $usoLabel   = 'USO ' . ($company->uso_persen ?? 1.25) . '%';
        $admLabel   = 'ADM ' . ($company->adm_persen ?? 2.5) . '%';
        $offset     = self::HEADER_OFFSET;

        return [
            BeforeSheet::class => function (BeforeSheet $event) use ($company, $ppnLabel, $bhpLabel, $usoLabel, $admLabel, $offset) {
                $sheet = $event->sheet->getDelegate();

                // ── Baris 1-7: Header perusahaan ──────────────────────────
                $sheet->setCellValue('A1', $company->official_name ?? $company->nama_perusahaan ?? 'PT. PERUSAHAAN');
                $sheet->setCellValue('A2', 'LAPORAN DAFTAR PELANGGAN');
                // Row 3: kosong
                $sheet->setCellValue('A4', 'NAMA MITRA');
                $sheet->setCellValue('B4', ':');
                $sheet->setCellValue('A5', 'ALAMAT');
                $sheet->setCellValue('B5', ':');
                $sheet->setCellValue('A6', 'TIKOR');
                $sheet->setCellValue('B6', ':');
                // Row 7: kosong

                // ── Baris 8: Header kolom (kuning) ────────────────────────
                $headers = ['NO', 'NAMA PELANGGAN', 'ALAMAT', 'NIK', 'HARGA PAKET', $ppnLabel, $bhpLabel, $usoLabel, $admLabel, 'TOTAL'];
                $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

                foreach ($cols as $i => $col) {
                    $sheet->setCellValue("{$col}{$offset}", $headers[$i]);
                }

                // Style baris 1
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
                // Style baris 2
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
                // Style baris 4-6
                foreach ([4, 5, 6] as $r) {
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);
                }

                // Style header kolom (baris 8): kuning, bold, border
                $sheet->getStyle("A{$offset}:J{$offset}")
                    ->getFont()->setBold(true)->setSize(10);
                $sheet->getStyle("A{$offset}:J{$offset}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFFF00');
                $sheet->getStyle("A{$offset}:J{$offset}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$offset}:J{$offset}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension($offset)->setRowHeight(22);

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
            },

            AfterSheet::class => function (AfterSheet $event) use ($offset) {
                $sheet      = $event->sheet->getDelegate();
                $lastRow    = $sheet->getHighestRow();
                $dataStart  = $offset + 1;

                if ($lastRow < $dataStart) {
                    return;
                }

                // Border pada baris data
                $sheet->getStyle("A{$dataStart}:J{$lastRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Kolom angka rata kanan + format ribuan
                foreach (['E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                    $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("{$col}{$dataStart}:{$col}{$lastRow}")
                        ->getNumberFormat()->setFormatCode('#,##0;-;-');
                }

                // Nomor (kolom A) rata tengah
                $sheet->getStyle("A{$dataStart}:A{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
