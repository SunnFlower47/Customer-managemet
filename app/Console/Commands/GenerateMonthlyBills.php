<?php

namespace App\Console\Commands;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for all active customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Check if bills already exist for this month
        $existingBills = Pembayaran::where('bulan_tagihan', $currentMonth)
            ->where('tahun_tagihan', $currentYear)
            ->count();

        if ($existingBills > 0) {
            $this->info("Bills for {$currentMonth}/{$currentYear} already exist. Skipping generation.");
            return;
        }

        $this->info("Generating bills for {$currentMonth}/{$currentYear}...");

        // Include customers with status 'aktif' or 'bayar double' (both can receive bills)
        $activeCustomers = Pelanggan::whereIn('status', ['aktif', 'bayar double'])
            ->with(['paket', 'penagih'])
            ->get();

        $generatedCount = 0;

        foreach ($activeCustomers as $customer) {
            // Check if bill already exists for this customer and month
            $existingBill = Pembayaran::where('pelanggan_id', $customer->id)
                ->where('bulan_tagihan', $currentMonth)
                ->where('tahun_tagihan', $currentYear)
                ->first();

            if (!$existingBill) {
                Pembayaran::create([
                    'pelanggan_id' => $customer->id,
                    'bulan_tagihan' => $currentMonth,
                    'tahun_tagihan' => $currentYear,
                    'jumlah' => $customer->paket->harga,
                    'status' => 'belum_bayar',
                    'penagih_id' => $customer->penagih_id,
                    'keterangan' => "Tagihan bulan {$currentMonth}/{$currentYear}",
                ]);

                $generatedCount++;
            }
        }

        $this->info("Successfully generated {$generatedCount} bills for {$currentMonth}/{$currentYear}");
    }
}

