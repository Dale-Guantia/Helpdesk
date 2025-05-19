<?php

namespace App\Filament\Widgets;

use App\Models\Status;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class Pie extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'pie';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Tickets Overview';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {

        $ticketStatuses = Status::select('id', 'status_name')->withCount(['tickets'])->get();
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
            ],
            'series' => $ticketStatuses->pluck('tickets_count')->toArray(),
            'labels' => $ticketStatuses->pluck('status_name')->toArray(),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
            'colors' => ['#EF5350','#10b981', '#f59e0b', '#ef4444', '#f59e0b'],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isHRDOAdmin();
    }

}
