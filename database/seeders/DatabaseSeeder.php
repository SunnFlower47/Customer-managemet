<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paket;
use App\Models\Penagih;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        // $admin = User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@wifi.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'admin',
        //     'aktif' => true,
        // ]);

        // Run essential seeders only
        $this->call([
            RolePermissionSeeder::class,
            CompanyProfileSeeder::class,
            TicketPaymentProofPermissionSeeder::class,
        ]);
    }
}
