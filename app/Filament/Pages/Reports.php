<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use App\Livewire;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;

class Reports extends Page
{
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.pages.reports';

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->isDepartmentHead()) {
            return [
                Action::make('downloadReportWithDates')
                    ->label('Download Report')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default(now()->startOfYear())
                            ->maxDate(fn (Get $get) => $get('end_date') ?: now())
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->default(now())
                            ->maxDate(now())
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
                        $endDate = Carbon::parse($data['end_date'])->format('Y-m-d');

                        $reportUrl = route('download_report', [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]);

                        // Dispatch the event to open the URL in a new tab
                        $this->dispatch('open-url-in-new-tab', url: $reportUrl);

                    })
                    ->modalHeading('Select Date Range')
                    ->modalSubmitActionLabel('Generate Report')
                    ->modalCancelActionLabel('Cancel'),
            ];
        } else {
            return [];
        }
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
        return 'desc';
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && ($user->isSuperAdmin() || $user->isDepartmentHead() || $user->isDivisionHead());
    }
}
