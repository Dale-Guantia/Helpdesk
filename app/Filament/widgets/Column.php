<?php

namespace App\Filament\Widgets;

use Filament\Forms\Components\Select;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Forms\Components\DatePicker;
use App\Models\Department;
use App\Models\Office;
use App\Models\Ticket;


class Column extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'column';

    protected static ?int $sort = 2;
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Tickets per Division';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

    // protected function getFormSchema(): array
    // {
    //     return [
    //         Select::make('department')
    //             ->options(
    //                 Department::pluck('department_name', 'id')->toArray()
    //             ),
    //     ];
    // }

    protected function getOptions(): array
    {
        $departmentId = $this->filterFormData['department'] ?? 1;

        // Get the department name
        $departmentName = Department::find($departmentId)?->department_name ?? 'Unknown Department';

        if (!$departmentId) {
            return [
                'chart' => ['type' => 'bar', 'height' => 300],
                'series' => [['name' => 'Total Tickets', 'data' => []]],
                'xaxis' => ['categories' => []],
            ];
        }

        // Get all offices under selected department
        // Get all offices under selected department
        $offices = Office::withCount(['tickets'])
            ->where('department_id', $departmentId)
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Total Tickets',
                    'data' => $offices->pluck('tickets_count')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $offices->pluck('office_name')->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '10px',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#0e2f66'],
            'title' => [
                'text' => "{$departmentName}",
                'align' => 'center',
                'style' => [
                    'fontSize' => '11px',
                    'fontWeight' => 'bold',
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isAgent();
    }
}
