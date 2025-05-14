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
            'office_name' => 'HRDO',
        ]);
        Office::create([
            'office_name' => 'RSP',
        ]);
        Office::create([
            'office_name' => 'PAYROLL',
        ]);
    }
}
