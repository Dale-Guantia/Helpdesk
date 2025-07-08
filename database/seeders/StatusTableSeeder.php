<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create([
            'status_name' => 'Pending',
            'badge_color' => 'warning',
        ]);
        Status::create([
            'status_name' => 'Resolved',
            'badge_color' => 'success',
        ]);
        Status::create([
            'status_name' => 'Unassigned',
            'badge_color' => 'gray',
        ]);
        Status::create([
            'status_name' => 'Reopened',
            'badge_color' => 'primary',
        ]);
    }
}
