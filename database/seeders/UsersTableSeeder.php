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
        ]);
        User::create([
            'name' => 'HRDO Division Head',
            'email' => 'divisionhead@example.com',
            'phone' => '09123456788',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 2,
        ]);
        User::create([
            'name' => 'HRDO Staff',
            'email' => 'staff@example.com',
            'phone' => '09123456788',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 3,
        ]);
        User::create([
            'name' => 'Engineer Head',
            'email' => 'divisionhead2@example.com',
            'phone' => '09123456788',
            'password' => bcrypt('12341234'),
            'department_id' => 4,
            'office_id' => null,
            'is_active' => 1,
            'role' => 2,
        ]);
        User::create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'phone' => '09123456787',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 3,
            'is_active' => 1,
            'role' => 4,
        ]);
    }
}
