<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::create([
            'office_name' => 'INFORMATION TECHNOLOGY',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'ADMINISTRATIVE',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'PAYROLL',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'RECORDS',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'CLAIMS AND BENEFITS',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'RSP',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'LEARNING AND DEVELOPMENT',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'PERFORMANCE MANAGEMENT',
            'department_id' => 1,
        ]);
    }
}
