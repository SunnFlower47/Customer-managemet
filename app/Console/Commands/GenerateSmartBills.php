<?php

namespace App\Console\Commands;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateSmartBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:generate-smart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate bills based on customer payment dates and check payment status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $currentDay = $today->day;

        $this->info("Checking bills for {$currentDay}/{$currentMonth}/{$currentYear}...");

        // IMPORTANT: DO NOT update existing payments - this violates IMMUTABLE principle
        // Historical data must remain unchanged for audit integrity
        // Only new payments will use current customer settings
        $this->info("Preserving historical data integrity - no updates to existing payments");

        // Get all active customers (aktif or bayar double - both can receive bills) with pagination for better performance
        $activeCustomers = Pelanggan::whereIn('status', ['aktif', 'bayar double'])
            ->with(['paket:id,nama_paket,harga', 'penagih:id,nama'])
            ->orderBy('tanggal_pembayaran')
            ->get();

        $this->info("Found {$activeCustomers->count()} active customers");

        $generatedCount = 0;
        $checkedCount = 0;
        $overdueCount = 0;
        $newCustomerCount = 0;
        $pendingCount = 0;
        $paidCount = 0;
        $overdueCustomers = [];

        // Progress bar for better UX
        $progressBar = $this->output->createProgressBar($activeCustomers->count());
        $progressBar->setFormat('Processing customers: %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Starting...');
        $progressBar->start();

        foreach ($activeCustomers as $customer) {
            $paymentDate = $customer->tanggal_pembayaran;
            $customerCreatedThisMonth = $customer->created_at->month == $currentMonth && $customer->created_at->year == $currentYear;

            // Check if today is the payment date for this customer OR if customer is new this month
            if ($currentDay == $paymentDate || $customerCreatedThisMonth) {
                $progressBar->setMessage("Processing {$customer->nama}...");

                // Check if bill already exists for this month
                $existingBill = Pembayaran::where('pelanggan_id', $customer->id)
                    ->where('bulan_tagihan', $currentMonth)
                    ->where('tahun_tagihan', $currentYear)
                    ->first();

                if (!$existingBill) {
                    // Generate new bill
                    $keterangan = $customerCreatedThisMonth
                        ? "Tagihan bulan {$currentMonth}/{$currentYear} - Pelanggan baru (ditambahkan: {$customer->created_at->format('d/m/Y')})"
                        : "Tagihan bulan {$currentMonth}/{$currentYear} - Auto generated pada tanggal {$paymentDate}";

                    // Generate unique payment code
                    $companyProfile = \App\Models\CompanyProfile::first();
                    $prefix = $companyProfile->payment_code_prefix ?? 'PAY';

                    do {
                        $kode = $prefix . date('Ymd') . rand(1000, 9999);
                    } while (Pembayaran::where('kode_pembayaran', $kode)->exists());

                    // Get the correct package price for this billing period
                    $billingDate = Carbon::create($currentYear, $currentMonth, 1);
                    $activePackage = $customer->getActivePackageForDate($billingDate);

                    // Use historical package price if available, otherwise use current package
                    $packagePrice = $activePackage ? $activePackage->price : $customer->paket->harga;
                    $packageName = $activePackage ? $activePackage->package->nama_paket : $customer->paket->nama_paket;

                    Pembayaran::create([
                        'kode_pembayaran' => $kode,
                        'pelanggan_id' => $customer->id,
                        'paket_id' => $activePackage ? $activePackage->package_id : $customer->paket_id,
                        'nama_paket' => $packageName,
                        'harga_paket' => $packagePrice,
                        'bulan_tagihan' => $currentMonth,
                        'tahun_tagihan' => $currentYear,
                        'jumlah' => $packagePrice,
                        'status' => 'belum_bayar',
                        'penagih_id' => $customer->penagih_id,
                        'nama_penagih' => $customer->penagih ? $customer->penagih->nama : null,
                        'keterangan' => $keterangan . " (Paket: {$packageName})",
                    ]);

                    $generatedCount++;
                    if ($customerCreatedThisMonth) {
                        $newCustomerCount++;
                    }
                } else {
                    // Check if customer has paid
                    if ($existingBill->status === 'belum_bayar') {
                        $pendingCount++;
                    } else {
                        $paidCount++;
                    }
                }

                $checkedCount++;
            }

            // Check for overdue payments (past payment date)
            if ($currentDay > $paymentDate) {
                $existingBill = Pembayaran::where('pelanggan_id', $customer->id)
                    ->where('bulan_tagihan', $currentMonth)
                    ->where('tahun_tagihan', $currentYear)
                    ->first();

                if ($existingBill && $existingBill->status === 'belum_bayar') {
                    $overdueDays = $currentDay - $paymentDate;
                    $overdueCount++;
                    $overdueCustomers[] = [
                        'nama' => $customer->nama,
                        'days' => $overdueDays,
                        'payment_date' => $paymentDate,
                        'penagih' => $customer->penagih ? $customer->penagih->nama : 'Tidak ada penagih'
                    ];
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Display concise summary
        $this->info("\n📊 SUMMARY:");
        $this->info("├─ Total active customers: {$activeCustomers->count()}");
        $this->info("├─ Customers processed today: {$checkedCount}");
        $this->info("├─ New bills generated: {$generatedCount}");
        $this->info("├─ Pending payments: {$pendingCount}");
        $this->info("├─ Paid customers: {$paidCount}");
        $this->info("└─ Overdue customers: {$overdueCount}");

        if ($newCustomerCount > 0) {
            $this->info("\n✅ {$newCustomerCount} new customers billed for this month!");
        }

        if ($generatedCount > 0) {
            $this->info("✅ {$generatedCount} new bills generated successfully!");
        }

        if ($overdueCount > 0) {
            $this->warn("\n⚠️  OVERDUE PAYMENTS ({$overdueCount} customers):");
            // Show only top 5 overdue customers to avoid spam
            $topOverdue = array_slice($overdueCustomers, 0, 5);
            foreach ($topOverdue as $overdue) {
                $this->warn("   • {$overdue['nama']} - {$overdue['days']} days overdue (Due: {$overdue['payment_date']})");
            }
            if (count($overdueCustomers) > 5) {
                $this->warn("   ... and " . (count($overdueCustomers) - 5) . " more customers");
            }
        } else {
            $this->info("\n✅ No overdue payments found");
        }

        if ($generatedCount === 0 && $overdueCount === 0) {
            $this->info("\n✅ All customers are up to date!");
        }
    }

}
