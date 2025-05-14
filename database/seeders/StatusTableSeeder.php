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
        ]);
        Status::create([
            'status_name' => 'Resolved',
        ]);
        Status::create([
            'status_name' => 'Unassigned',
        ]);
    }
}
