<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Setup Spatie Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@hmphospitality.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create Staff User
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@hmphospitality.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staffUser->assignRole($staffRole);
    }
}
