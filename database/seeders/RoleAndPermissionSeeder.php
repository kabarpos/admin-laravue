<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles dan permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Hapus role editor jika ada
        Role::where('name', 'editor')->delete();

        // Buat permissions untuk modul user
        $this->createPermissions([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'approve users',
            'reject users',
            'block users',
            
            // Permissions untuk modul role
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permissions untuk modul permission
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Permissions untuk user biasa
            'view own profile',
            'edit own profile'
        ]);

        // Buat role admin dan berikan semua permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Buat role user biasa dan berikan beberapa permissions
        $userRole = Role::firstOrCreate(['name' => 'user']);
        // User biasa hanya memiliki akses ke profil sendiri
        $userRole->syncPermissions(['view own profile', 'edit own profile']);
    }
    
    /**
     * Buat permission jika belum ada
     */
    private function createPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
