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
            'colors' => ['#ff7572', '#b56bff', '#3496ff', '#57caff', '#1dffb0', '#58fa5d', '#e3f85d', '#ffd152', '#ff9a42', '#ee75de' ],
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
