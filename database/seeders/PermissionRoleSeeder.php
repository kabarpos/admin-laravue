<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat role admin jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Daftar permission yang akan dibuat
        $permissions = [
            // Permission untuk user management
            'manage_users',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Permission untuk role management
            'manage_roles',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            
            // Permission untuk permission management
            'manage_permissions',
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            
            // Permission untuk pengaturan
            'manage_settings',
            'view_settings',
            'edit_settings',
            
            // Permission untuk dashboard admin
            'access_admin_dashboard',
        ];
        
        // Membuat permission dan menghubungkannya dengan role admin
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            
            // Tambahkan permission ke role admin
            if (!$adminRole->hasPermissionTo($permissionName)) {
                $adminRole->givePermissionTo($permission);
            }
        }
        
        $this->command->info('Permissions and admin role created successfully!');
    }
}
