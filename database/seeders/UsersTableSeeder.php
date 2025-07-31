<?php

namespace Database\Seeders;

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
            'office_id' => 2,
            'is_active' => 1,
            'role' => 1,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Deaprtment Head',
            'username' => 'departmenthead',
            'email' => 'departmenthead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 1,
            'is_active' => 1,
            'role' => 5,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'IT Head',
            'username' => 'it',
            'email' => 'ithead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 2,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Admin Head',
            'username' => 'admin',
            'email' => 'adminhead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 3,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Payroll Head',
            'username' => 'payroll',
            'email' => 'payrollhead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 4,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Records Head',
            'username' => 'records',
            'email' => 'recordshead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 5,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'Claims Head',
            'username' => 'claims',
            'email' => 'claimshead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 6,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'RSP Head',
            'username' => 'rsp',
            'email' => 'rsphead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 7,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'L&D Head',
            'username' => 'lnd',
            'email' => 'lndhead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 8,
            'is_active' => 1,
            'role' => 2,
            'email_verified_at' => '2025-06-30 15:30:10'
        ]);
        User::create([
            'name' => 'PM Head',
            'username' => 'pm',
            'email' => 'pmhead@example.com',
            'password' => bcrypt('12341234'),
            'department_id' => 1,
            'office_id' => 9,
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
            'office_id' => 3,
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
