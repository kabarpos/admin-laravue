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

        // Buat permissions untuk modul user
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'approve users']);
        Permission::create(['name' => 'reject users']);
        Permission::create(['name' => 'block users']);

        // Buat permissions untuk modul role
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'edit roles']);
        Permission::create(['name' => 'delete roles']);

        // Buat permissions untuk modul permission
        Permission::create(['name' => 'view permissions']);
        Permission::create(['name' => 'create permissions']);
        Permission::create(['name' => 'edit permissions']);
        Permission::create(['name' => 'delete permissions']);

        // Buat role admin dan berikan semua permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Buat role user biasa dan berikan beberapa permissions
        $userRole = Role::create(['name' => 'user']);
        // User biasa tidak memiliki akses ke admin panel
    }
}
