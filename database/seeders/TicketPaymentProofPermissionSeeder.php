<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TicketPaymentProofPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for Ticket Management
        $ticketPermissions = [
            'view-ticket',
            'create-ticket',
            'edit-ticket',
            'delete-ticket',
            'assign-ticket',
            'resolve-ticket',
        ];

        // Create permissions for Payment Proof Management
        $paymentProofPermissions = [
            'view-payment-proof',
            'verify-payment-proof',
            'reject-payment-proof',
            'download-payment-proof',
        ];

        // Create all permissions
        $allPermissions = array_merge($ticketPermissions, $paymentProofPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo([
            'view-ticket', 'create-ticket', 'edit-ticket', 'delete-ticket', 'assign-ticket', 'resolve-ticket',
            'view-payment-proof', 'verify-payment-proof', 'reject-payment-proof', 'download-payment-proof'
        ]);

        // Admin - All permissions except delete
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view-ticket', 'create-ticket', 'edit-ticket', 'assign-ticket', 'resolve-ticket',
            'view-payment-proof', 'verify-payment-proof', 'reject-payment-proof', 'download-payment-proof'
        ]);

        // Manager - View and edit permissions
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view-ticket', 'edit-ticket', 'assign-ticket', 'resolve-ticket',
            'view-payment-proof', 'verify-payment-proof', 'reject-payment-proof', 'download-payment-proof'
        ]);

        // Staff - Limited permissions
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->givePermissionTo([
            'view-ticket', 'edit-ticket',
            'view-payment-proof', 'verify-payment-proof', 'download-payment-proof'
        ]);

        // Customer Service - Ticket and payment proof management
        $customerService = Role::firstOrCreate(['name' => 'Customer Service']);
        $customerService->givePermissionTo([
            'view-ticket', 'create-ticket', 'edit-ticket', 'assign-ticket', 'resolve-ticket',
            'view-payment-proof', 'verify-payment-proof', 'reject-payment-proof', 'download-payment-proof'
        ]);
    }
}
