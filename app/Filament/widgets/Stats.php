<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Stats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Total Tickets', Ticket::count()),
            Stat::make('Resolved Tickets', Ticket::where('status_id', '4')->count()),
            Stat::make('Unassigned Tickets', Ticket::where('status_id', '3')->count()),
        ];
    }
}
