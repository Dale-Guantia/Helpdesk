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
            'email' => 'superadmin@example.com',
            'phone' => '09123456789',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 1,
            'is_active' => 1,
            'role' => 1,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Division Head',
            'email' => 'divisionhead@example.com',
            'phone' => '09123456788',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'phone' => '09123456788',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 3,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'phone' => '09123456787',
            'password' => bcrypt('12341234'),
            'is_active' => 1,
            'role' => 4,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
    }
}
