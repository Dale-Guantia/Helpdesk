<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Office;
use App\Models\Ticket;
use App\Models\ProblemCategory;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Stats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->icon('heroicon-o-user-group')
                ->color('admin')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Tickets', Ticket::count())
                ->icon('heroicon-o-ticket')
                ->color('pm')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Divisions', Office::count())
                ->icon('heroicon-o-building-office')
                ->color('payroll')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Issues', ProblemCategory::count())
                ->icon('heroicon-o-exclamation-triangle')
                ->color('deptHead')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isAgent();
    }
}
