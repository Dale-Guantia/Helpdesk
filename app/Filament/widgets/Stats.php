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
            Stat::make('Users', User::count())
                // ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Tickets', Ticket::count())
                // ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Offices', Office::count())
                // ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Problem Categories', ProblemCategory::count())
                // ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isHRDOAdmin();
    }
}
