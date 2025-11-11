<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use App\Livewire;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Forms\Components\Checkbox;

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
                    ->label('Employee Care Report Download')
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
                        Checkbox::make('include_zero_tickets')
                            ->label('Include records with zero activity')
                            ->default(false),
                    ])
                    ->action(function (array $data) {
                        $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
                        $endDate = Carbon::parse($data['end_date'])->format('Y-m-d');

                        // FIX: Get the boolean value and convert it to the string 'true' or 'false'
                        $includeZero = $data['include_zero_tickets'] ? 'true' : 'false';

                        $reportUrl = route('download_report', [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            // ADDED: Pass the flag to the report controller
                            'include_zero_tickets' => $includeZero,
                        ]);

                        // Dispatch the event to open the URL in a new tab
                        $this->dispatch('open-url-in-new-tab', url: $reportUrl);

                    })
                    ->modalHeading('Select Date Range')
                    ->modalSubmitActionLabel('Generate Report')
                    ->modalCancelActionLabel('Cancel'),

                // MODIFIED CSS REPORT ACTION with Date Range and Checkbox
                Action::make('cssReportDownload')
                    ->label('CSS Report Download')
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
                        Checkbox::make('include_zero_surveys') // New Checkbox for CSS report
                            ->label('Include staff with zero completed surveys')
                            ->default(false),
                    ])
                    ->action(function (array $data) {
                        $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
                        $endDate = Carbon::parse($data['end_date'])->format('Y-m-d');

                        // NEW: Capture the boolean value for zero surveys
                        $includeZero = $data['include_zero_surveys'] ? 'true' : 'false';

                        $reportUrl = route('download_css_report', [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'include_zero_surveys' => $includeZero, // Pass to controller
                        ]);

                        // Dispatch the event to open the URL in a new tab
                        $this->dispatch('open-url-in-new-tab', url: $reportUrl);
                    })
                    ->modalHeading('Select CSS Report Date Range')
                    ->modalSubmitActionLabel('Generate CSS Report')
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
