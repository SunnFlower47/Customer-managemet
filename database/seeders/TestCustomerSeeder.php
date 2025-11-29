<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Penagih;
use Illuminate\Support\Facades\Hash;

class TestCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test package if not exists
        $paket = Paket::firstOrCreate(
            ['nama_paket' => 'Paket Test 10 Mbps'],
            [
                'harga' => 150000,
                'deskripsi' => 'Paket test untuk customer portal - 10 Mbps',
                'aktif' => true,
            ]
        );

        // Create test collector if not exists
        $penagih = Penagih::firstOrCreate(
            ['email' => 'test.penagih@example.com'],
            [
                'nama' => 'Test Penagih',
                'no_hp' => '081234567890',
                'alamat' => 'Alamat Test Penagih',
                'aktif' => true,
            ]
        );

        // Create test customers
        $customers = [
            [
                'nama' => 'John Doe',
                'pppoe' => 'john.doe',
                'alamat' => 'Jl. Test No. 123',
                'no_hp' => '081234567890',
                'paket_id' => $paket->id,
                'penagih_id' => $penagih->id,
                'tanggal_mulai' => now()->subMonths(6),
                'tanggal_pembayaran' => 15,
                'status' => 'aktif',
                'password' => Hash::make('123456'), // Password default sama untuk semua
                'is_default_password' => true,
            ],
            [
                'nama' => 'Jane Smith',
                'pppoe' => 'jane.smith',
                'alamat' => 'Jl. Test No. 456',
                'no_hp' => '081234567891',
                'paket_id' => $paket->id,
                'penagih_id' => $penagih->id,
                'tanggal_mulai' => now()->subMonths(3),
                'tanggal_pembayaran' => 20,
                'status' => 'aktif',
                'password' => Hash::make('123456'), // Password default sama untuk semua
                'is_default_password' => true,
            ],
            [
                'nama' => 'Bob Wilson',
                'pppoe' => 'bob.wilson',
                'alamat' => 'Jl. Test No. 789',
                'no_hp' => '081234567892',
                'paket_id' => $paket->id,
                'penagih_id' => $penagih->id,
                'tanggal_mulai' => now()->subMonths(1),
                'tanggal_pembayaran' => 10,
                'status' => 'aktif',
                'password' => Hash::make('123456'), // Password default sama untuk semua
                'is_default_password' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            Pelanggan::firstOrCreate(
                ['no_hp' => $customerData['no_hp']],
                $customerData
            );
        }

        $this->command->info('Test customers created successfully!');
        $this->command->info('Test login credentials:');
        $this->command->info('Password default untuk semua: 123456');
        $this->command->info('1. Username: 081234567890, Password: 123456');
        $this->command->info('2. Username: john.doe, Password: 123456');
        $this->command->info('3. Username: 081234567891, Password: 123456');
        $this->command->info('4. Username: jane.smith, Password: 123456');
        $this->command->info('5. Username: 081234567892, Password: 123456');
        $this->command->info('6. Username: bob.wilson, Password: 123456');
    }
}
