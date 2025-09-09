<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Pelanggan
            'view-pelanggan',
            'create-pelanggan',
            'edit-pelanggan',
            'delete-pelanggan',
            'export-pelanggan',

            // Paket
            'view-paket',
            'create-paket',
            'edit-paket',
            'delete-paket',

            // Penagih
            'view-penagih',
            'create-penagih',
            'edit-penagih',
            'delete-penagih',

            // Pembayaran
            'view-pembayaran',
            'create-pembayaran',
            'edit-pembayaran',
            'delete-pembayaran',
            'export-pembayaran',
            'update-status-pembayaran',
            'print-invoice-pembayaran',

            // Pengeluaran
            'view-pengeluaran',
            'create-pengeluaran',
            'edit-pengeluaran',
            'delete-pengeluaran',
            'export-pengeluaran',

            // Laporan
            'view-laporan-pendapatan',
            'view-laporan-pengeluaran',
            'view-laporan-laba-rugi',
            'export-laporan',

            // User Management
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',

            // Settings
            'view-settings',
            'manage-roles',
            'manage-permissions',
            'backup-database',
            'restore-database',
            'manage-company-profile',

            // Audit Trail
            'view-audit-trail',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // Admin - Full access
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Operator - Can manage customers, payments, and reports
        $operatorRole = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $operatorRole->givePermissionTo([
            'view-dashboard',
            'view-pelanggan',
            'create-pelanggan',
            'edit-pelanggan',
            'export-pelanggan',
            'view-paket',
            'view-penagih',
            'view-pembayaran',
            'create-pembayaran',
            'edit-pembayaran',
            'export-pembayaran',
            'update-status-pembayaran',
            'print-invoice-pembayaran',
            'view-pengeluaran',
            'create-pengeluaran',
            'edit-pengeluaran',
            'export-pengeluaran',
            'view-laporan-pendapatan',
            'view-laporan-pengeluaran',
            'view-laporan-laba-rugi',
            'export-laporan',
        ]);

        // Penagih - Limited access for collectors
        $penagihRole = Role::firstOrCreate(['name' => 'penagih', 'guard_name' => 'web']);
        $penagihRole->givePermissionTo([
            'view-dashboard',
            'view-pelanggan',
            'view-pembayaran',
            'update-status-pembayaran',
            'print-invoice-pembayaran',
        ]);

        // Assign admin role to existing admin users
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $user) {
            $user->assignRole('admin');
        }

        // Assign penagih role to existing penagih users
        $penagihUsers = User::where('role', 'penagih')->get();
        foreach ($penagihUsers as $user) {
            $user->assignRole('penagih');
        }
    }
}
