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
            'office_code' => 'IT',
            'office_name' => 'INFORMATION TECHNOLOGY',
        ]);
        Office::create([
            'office_code' => 'ADMIN',
            'office_name' => 'ADMINISTRATION DIVISION',
        ]);
        Office::create([
            'office_code' => 'PAY',
            'office_name' => 'PAYROLL DIVISION',
        ]);
        Office::create([
            'office_code' => 'REC',
            'office_name' => 'RECORDS DIVISION',
        ]);
        Office::create([
            'office_code' => 'CAB',
            'office_name' => 'CLAIMS AND BENEFITS DIVISION',
        ]);
        Office::create([
            'office_code' => 'APT',
            'office_name' => 'APPOINTMENT DIVISION',
        ]);
        Office::create([
            'office_code' => 'LND',
            'office_name' => 'LEARNING AND DEVELOPMENT DIVISION',
        ]);
    }
}
