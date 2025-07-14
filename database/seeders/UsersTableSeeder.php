<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 1,
            'is_active' => 1,
            'role' => 1,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Division Head',
            'username' => 'divisionhead',
            'email' => 'divisionhead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'HRDO Staff',
            'username' => 'hrdostaff',
            'email' => 'hrdostaff@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 3,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Guest',
            'username' => 'guest',
            'email' => 'guest@example.com',
            'password' => bcrypt('12341234'),
            'is_active' => 1,
            'role' => 4,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
    }
}
