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
            'category_name' => 'IT PROBLEM',
        ]);
        ProblemCategory::create([
            'office_id' => 2,
            'category_name' => 'ADMIN PROBLEM',
        ]);
        ProblemCategory::create([
            'office_id' => 3,
            'category_name' => 'PAYROLL PROBLEM',
        ]);
                ProblemCategory::create([
            'office_id' => 4,
            'category_name' => 'RECORDS PROBLEM',
        ]);
        ProblemCategory::create([
            'office_id' => 5,
            'category_name' => 'CLAIMS AND BENEFITS PROBLEM',
        ]);
        ProblemCategory::create([
            'office_id' => 6,
            'category_name' => 'APPOINTMENT PROBLEM',
        ]);
        ProblemCategory::create([
            'office_id' => 7,
            'category_name' => 'LEARNING AND DEVELOPMENT PROBLEM',
        ]);
    }
}
