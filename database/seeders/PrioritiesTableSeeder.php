<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::create([
            'priority_name' => 'High',
        ]);
        Priority::create([
            'priority_name' => 'Medium',
        ]);
        Priority::create([
            'priority_name' => 'Low',
        ]);
    }
}
