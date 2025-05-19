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
            Stat::make('Users', User::count()),
            Stat::make('Tickets', Ticket::count()),
            Stat::make('Offices', Office::count()),
            Stat::make('Problem Categories', ProblemCategory::count()),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isHRDOAdmin();
    }
}
