<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view-dashboard',
            'manage-ruangan',
            'manage-pegawai',
            'manage-shift',
            'manage-users',
            'manage-roles',
            'manage-jadwal',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $kepalaRuanganRole = Role::firstOrCreate(['name' => 'kepala_ruangan']);
        $kepalaRuanganRole->syncPermissions(['view-dashboard', 'manage-jadwal', 'manage-pegawai']);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->syncPermissions(['view-dashboard']);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(['view-dashboard']);
    }
}
