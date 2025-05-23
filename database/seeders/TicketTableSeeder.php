<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::create([
            'user_id' => 1,
            'reference_id' => 'IT1-052225',
            'office_id' => 1,
            'problem_category_id' => 1,
            'priority_id' => 1,
            'status_id' => 1,
            'title' => 'Test title',
            'description' => 'Test message',
        ]);
    }
}
