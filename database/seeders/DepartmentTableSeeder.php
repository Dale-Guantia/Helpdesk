<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create([
            'department_name' => 'CITY HUMAN RESOURCE DEVELOPMENT OFFICE',
        ]);
        Department::create([
            'department_name' => 'CITY ACCOUNTING OFFICE',
        ]);
        Department::create([
            'department_name' => 'CITY ADMINISTRATOR OFFICE',
        ]);
        Department::create([
            'department_name' => 'CITY ENGINEER\'S OFFICE',
        ]);
        Department::create([
            'department_name' => 'OFFICE ON GENERAL SERVICES',
        ]);
        Department::create([
            'department_name' => 'CITY HEALTH DEPARTMENT',
        ]);
        Department::create([
            'department_name' => 'MANAGEMENT INFORMATION SYSTEMS OFFICE',
        ]);
    }
}
