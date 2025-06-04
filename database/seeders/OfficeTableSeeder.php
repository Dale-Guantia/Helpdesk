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
            'office_name' => 'ADMINISTRATION DIVISION',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'PAYROLL DIVISION',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'RECORDS DIVISION',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'CLAIMS AND BENEFITS DIVISION',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'APPOINTMENT DIVISION',
            'department_id' => 1,
        ]);
        Office::create([
            'office_name' => 'LEARNING AND DEVELOPMENT DIVISION',
            'department_id' => 1,
        ]);
    }
}
