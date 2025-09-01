<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Office;
use App\Models\Ticket;

class ReportsPieChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'reportsPieChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Tickets per Division';

    protected static ?int $contentHeight = 330;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $ticketCount = Office::select('office_name')->where('department_id',1)->withcount(['tickets'])->get();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
                'width' => '100%',
            ],
            'colors' => ['#008ffb', '#00e396', '#feb019', '#ff4560', '#775dd0', '#60ffff', '#b94479'],
            'series' => $ticketCount->pluck('tickets_count')->toArray(),
            'labels' => $ticketCount->pluck('office_name')->toArray(),
            // 'labels' => ['IT', 'ADMIN', 'PAYROLL', 'RECORDS', 'CLAIMS', 'RSP', 'L&D', 'PM'],
            'legend' => [
                'position' => 'bottom',
                'fontSize' => '8.5px',
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isDepartmentHead();
    }
}
