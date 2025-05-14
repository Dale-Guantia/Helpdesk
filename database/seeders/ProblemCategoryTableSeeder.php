<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProblemCategory;

class ProblemCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProblemCategory::create([
            'office_id' => 1,
            'category_name' => 'Problem 1 - HRDO',
        ]);
        ProblemCategory::create([
            'office_id' => 2,
            'category_name' => 'Problem 1 - RSP',
        ]);
        ProblemCategory::create([
            'office_id' => 3,
            'category_name' => 'Problem 1 - PAYROLL',
        ]);
    }
}
