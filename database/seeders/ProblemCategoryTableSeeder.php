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
            'department_id' => 1,
            'office_id' => 1,
            'category_name' => 'IT ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 1,
            'office_id' => 2,
            'category_name' => 'ADMIN ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 1,
            'office_id' => 3,
            'category_name' => 'PAYROLL ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 1,
            'office_id' => 4,
            'category_name' => 'RECORDS ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 1,
            'office_id' => 5,
            'category_name' => 'CLAIMS AND BENEFITS ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 1,
            'office_id' => 6,
            'category_name' => 'APPOINTMENT ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 1,
            'office_id' => 7,
            'category_name' => 'LEARNING AND DEVELOPMENT ISSUE',
        ]);
        ProblemCategory::create([
            'department_id' => 4,
            'office_id' => null,
            'category_name' => 'ENGINNEERING ISSUE',
        ]);
    }
}
