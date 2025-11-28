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

    protected function generateColors(int $count): array
    {
        // Define a palette of distinct colors (using bright, modern hex codes)
        $palette = [
            '#ff7572',
            '#b56bff',
            '#3496ff',
            '#1dffb0',
            '#57caff',
            '#58fa5d',
            '#e3f85d',
            '#ffd152',
            '#ff9a42',
            '#ee75de',
        ];

        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            // Cycle through the palette array
            $colors[] = $palette[$i % count($palette)];
        }
        return $colors;
    }


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
        $offices = Office::withCount(['tickets'])
            ->where('department_id', $departmentId)
            ->get();

        $barColors = $this->generateColors($offices->count());

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 340,
                'toolbar' => ['show' => true],
            ],

            'legend' => [
                'show' => false,
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
                        'fontWeight' => 'bold',
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
            'colors' => $barColors,

            'plotOptions' => [
                'bar' => [
                    'distributed' => true,
                    'horizontal' => false,
                ],
            ],

            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '12px',
                    'colors' => ['#000']
                ]
            ],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isAgent();
    }
}
