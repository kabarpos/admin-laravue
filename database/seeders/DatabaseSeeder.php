<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles dan permissions terlebih dahulu
        $this->call(RoleAndPermissionSeeder::class);
        
        // Seed user admin
        $this->call(AdminUserSeeder::class);
    }
}
