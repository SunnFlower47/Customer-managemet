<?php

namespace App\Console\Commands;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMonthlyBillsAccurate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:generate-monthly-accurate {month?} {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for all customers based on their payment dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->argument('month') ?: Carbon::now()->month;
        $year = $this->argument('year') ?: Carbon::now()->year;

        $this->info("Generating bills for {$month}/{$year}...");

        // Validate month and year
        if ($month < 1 || $month > 12) {
            $this->error('Invalid month. Please provide month between 1-12.');
            return 1;
        }

        if ($year < 2020 || $year > 2030) {
            $this->error('Invalid year. Please provide year between 2020-2030.');
            return 1;
        }

        // Check if bills already exist for this month/year
        $existingBills = Pembayaran::where('bulan_tagihan', $month)
            ->where('tahun_tagihan', $year)
            ->count();

        if ($existingBills > 0) {
            if (!$this->confirm("Bills for {$month}/{$year} already exist ({$existingBills} bills). Continue anyway?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Get all active customers
        $activeCustomers = Pelanggan::where('status', 'aktif')
            ->with(['paket:id,nama_paket,harga', 'penagih:id,nama'])
            ->orderBy('tanggal_pembayaran')
            ->get();

        $generatedCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($activeCustomers->count());
        $progressBar->start();

        foreach ($activeCustomers as $customer) {
            // Check if bill already exists for this customer and month/year
            $existingBill = Pembayaran::where('pelanggan_id', $customer->id)
                ->where('bulan_tagihan', $month)
                ->where('tahun_tagihan', $year)
                ->first();

            if (!$existingBill) {
                // Generate new bill
                Pembayaran::create([
                    'pelanggan_id' => $customer->id,
                    'bulan_tagihan' => $month,
                    'tahun_tagihan' => $year,
                    'jumlah' => $customer->paket->harga,
                    'status' => 'belum_bayar',
                    'penagih_id' => $customer->penagih_id,
                    'keterangan' => "Tagihan bulan {$month}/{$year} - Generated for payment date {$customer->tanggal_pembayaran}",
                ]);

                $generatedCount++;
            } else {
                $skippedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("\nSummary:");
        $this->info("- Total active customers: {$activeCustomers->count()}");
        $this->info("- New bills generated: {$generatedCount}");
        $this->info("- Bills already exist: {$skippedCount}");
        $this->info("- Total bills for {$month}/{$year}: " . ($generatedCount + $skippedCount));

        if ($generatedCount > 0) {
            $this->info("\n✓ Successfully generated {$generatedCount} new bills for {$month}/{$year}");
        } else {
            $this->info("\nℹ No new bills generated. All customers already have bills for {$month}/{$year}");
        }

        return 0;
    }
}
