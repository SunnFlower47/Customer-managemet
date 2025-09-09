<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Penagih;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class PelangganFromExcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get default paket 150k
        $paket150k = Paket::where('nama_paket', 'like', '%150%')->first();
        if (!$paket150k) {
            $paket150k = Paket::first(); // Fallback to first paket
        }

        // Get first penagih as default
        $penagih = Penagih::where('aktif', true)->first();

        if (!$paket150k || !$penagih) {
            $this->command->error('Paket 150k atau Penagih tidak ditemukan. Pastikan data sudah ada.');
            return;
        }

        $this->command->info("Menggunakan paket: {$paket150k->nama_paket}");
        $this->command->info("Menggunakan penagih: {$penagih->nama}");

        // Path to Excel file
        $excelPath = base_path('pelanggan.xlsx');

        if (!file_exists($excelPath)) {
            $this->command->error("File Excel tidak ditemukan: {$excelPath}");
            return;
        }

        try {
            // Load Excel file
            $spreadsheet = IOFactory::load($excelPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $this->command->info("Membaca file Excel: " . count($rows) . " baris data");

            // Skip header row (assuming first row is header)
            $dataRows = array_slice($rows, 1);

            $created = 0;
            $skipped = 0;

            foreach ($dataRows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Extract data from Excel columns
                // Columns: [nama, pppoe, no_hp, alamat, (kosong), status, paket]
                $nama = trim($row[0] ?? '');
                $pppoe = trim($row[1] ?? '');
                $no_hp_raw = trim($row[2] ?? '');
                $alamat = trim($row[3] ?? '') ?: '-';
                $status_excel = trim($row[5] ?? 'aktif'); // Kolom 6 (index 5)
                $paket_excel = trim($row[6] ?? ''); // Kolom 7 (index 6)

                // Format nomor HP: tambahkan 0 di depan jika belum ada
                if (!empty($no_hp_raw)) {
                    $no_hp = $no_hp_raw;
                    // Jika nomor HP tidak dimulai dengan 0, tambahkan 0 di depan
                    if (!str_starts_with($no_hp, '0')) {
                        $no_hp = '0' . $no_hp;
                    }
                } else {
                    $no_hp = '-';
                }

                // Skip if required fields are empty
                if (empty($nama) || empty($pppoe)) {
                    $this->command->warn("Baris " . ($index + 2) . " dilewati: nama atau pppoe kosong");
                    $skipped++;
                    continue;
                }

                // Check if pelanggan already exists
                if (Pelanggan::where('pppoe', $pppoe)->exists()) {
                    $this->command->warn("Pelanggan dengan PPPoE '{$pppoe}' sudah ada, dilewati");
                    $skipped++;
                    continue;
                }

                // Create pelanggan
                $pelanggan = Pelanggan::create([
                    'nama' => $nama,
                    'pppoe' => $pppoe,
                    'no_hp' => $no_hp,
                    'alamat' => $alamat,
                    'paket_id' => $paket150k->id,
                    'penagih_id' => $penagih->id,
                    'tanggal_mulai' => now()->format('Y-m-d'),
                    'tanggal_pembayaran' => 10, // Default tanggal 10
                    'status' => $status_excel === 'aktif' ? 'aktif' : 'nonaktif',
                ]);

                // Create initial package history
                \App\Models\CustomerPackage::create([
                    'customer_id' => $pelanggan->id,
                    'package_id' => $paket150k->id,
                    'start_date' => $pelanggan->tanggal_mulai,
                    'end_date' => null,
                    'price' => $paket150k->harga,
                ]);

                $created++;
                $this->command->info("✓ Dibuat: {$nama} ({$pppoe})");
            }

            $this->command->info("\n=== HASIL IMPORT ===");
            $this->command->info("Berhasil dibuat: {$created} pelanggan");
            $this->command->info("Dilewati: {$skipped} pelanggan");
            $this->command->info("Total data: " . ($created + $skipped));

        } catch (\Exception $e) {
            $this->command->error("Error membaca file Excel: " . $e->getMessage());
        }
    }
}
