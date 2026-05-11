<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OltPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // OLT related permissions
        $permissions = [
            // OLT Monitoring
            'view-olt',
            'manage-olt',
            'sync-olt',

            // ONU Management
            'view-onu',
            'manage-onu',
            'reboot-onu',

            // VLAN Database
            'view-vlan',
            'manage-vlan',

            // Speed Profiles
            'view-speed-profile',
            'manage-speed-profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign to Admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assign viewing permissions to Operator role
        $operatorRole = Role::where('name', 'operator')->first();
        if ($operatorRole) {
            $operatorRole->givePermissionTo([
                'view-olt',
                'view-onu',
                'view-vlan',
                'view-speed-profile',
            ]);
        }
    }
}
