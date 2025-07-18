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
                ->color('primary')
                ->chart([1, 1, 1, 1, 1, 1, 1]),
            Stat::make('Total Tickets', Ticket::count())
                ->icon('heroicon-o-ticket')
                ->color('primary')
                ->chart([1, 1, 1, 1, 1, 1, 1]),
            Stat::make('Total Divisions', Office::count())
                ->icon('heroicon-o-building-office')
                ->color('primary')
                ->chart([1, 1, 1, 1, 1, 1, 1]),
            Stat::make('Total Issues', ProblemCategory::count())
                ->icon('heroicon-o-exclamation-triangle')
                ->color('primary')
                ->chart([1, 1, 1, 1, 1, 1, 1]),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isAgent();
    }
}
