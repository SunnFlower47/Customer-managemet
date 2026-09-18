<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Penagih;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin (Full Akses)
        $admin = User::updateOrCreate(
            ['email' => 'admin@wifi.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'aktif' => true,
            ]
        );
        if (Role::where('name', 'admin')->exists()) {
            $admin->syncRoles(['admin']);
        }

        // 2. Akun Operator (Manajemen Pelanggan, Tagihan, Laporan)
        $operator = User::updateOrCreate(
            ['email' => 'operator@wifi.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'aktif' => true,
            ]
        );
        if (Role::where('name', 'operator')->exists()) {
            $operator->syncRoles(['operator']);
        }

        // 3. Akun Penagih (Petugas Lapangan / Kolektor)
        $penagihUser = User::updateOrCreate(
            ['email' => 'penagih@wifi.com'],
            [
                'name' => 'Petugas Penagih',
                'password' => Hash::make('password'),
                'role' => 'penagih',
                'aktif' => true,
            ]
        );
        if (Role::where('name', 'penagih')->exists()) {
            $penagihUser->syncRoles(['penagih']);
        }

        // Hubungkan akun user penagih ke tabel penagihs jika belum ada
        Penagih::updateOrCreate(
            ['user_id' => $penagihUser->id],
            [
                'nama' => $penagihUser->name,
                'email' => $penagihUser->email,
                'no_hp' => '081234567890',
                'alamat' => 'Kantor Operasional',
                'aktif' => true,
            ]
        );

        $this->command->info('User accounts successfully seeded!');
    }
}
