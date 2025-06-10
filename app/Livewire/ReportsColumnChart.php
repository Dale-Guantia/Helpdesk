<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ReportsColumnChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'reportsColumnChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Tickets per month';

    protected static ?int $contentHeight = 315;

    public function mount(): void
    {
        $this->filter ??= now()->year;
    }

    protected function getFilters(): array
    {
        return collect(range(now()->year, now()->year - 5))
            ->mapWithKeys(fn ($year) => [$year => (string) $year])
            ->all();
    }


    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // $selectedYear = $this->filter;

        // $ticketCounts = array_fill(1, 12, 0);

        // $tickets = DB::table('tickets') // Specify your table name directly
        //     ->select(
        //         DB::raw('MONTH(created_at) as month'),
        //         DB::raw('COUNT(*) as count')
        //     )
        //     ->whereYear('created_at', $selectedYear)
        //     ->groupBy(DB::raw('MONTH(created_at)'))
        //     ->orderBy('month')
        //     ->get();

        // foreach ($tickets as $ticket) {
        //     $ticketCounts[$ticket->month] = $ticket->count;
        // }

        // $data = array_values($ticketCounts);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Total Tickets',
                    'data' => [0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0],
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
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
            'colors' => ['#f59e0b'],
        ];
    }
}
