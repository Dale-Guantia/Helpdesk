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
            'badge_color' => 'danger',
        ]);
        Priority::create([
            'priority_name' => 'Medium',
            'badge_color' => 'warning',
        ]);
        Priority::create([
            'priority_name' => 'Low',
            'badge_color' => 'primary',
        ]);
    }
}
