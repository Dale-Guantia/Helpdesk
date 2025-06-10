<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use App\Livewire;

class Reports extends Page
{
    protected static ?int $navigationSort = 8;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.reports';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Download report')
                ->icon('heroicon-m-arrow-down-tray')
                ->url(route('download_report')) // your PDF route here
                ->openUrlInNewTab()
                ->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Livewire\ReportsColumnChart::class,
            Livewire\ReportsPieChart::class,
        ];
    }

    public function getDefaultTableSortDirection(): ?string
    {
        return 'desc'; // sort highest to lowest
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && ($user->isSuperAdmin() || $user->isDivisionHead());
    }
}
