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

    /**
     * Chart content height
     *
     * @var int|null
     */
    protected static ?int $contentHeight = 315;

    protected function getFilters(): ?array
    {
        $years = [];
        $currentYear = Carbon::now()->year;

        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[$i] = (string) $i;
        }

        return $years;
    }

    protected function getDefaultFilter(): ?string
    {
        return (string) Carbon::now()->year; // Default to the current year
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // This is the most crucial line for ensuring the year is set.
        // It guarantees that $selectedYear always has a value,
        // either from the user's selection ($this->filter) or the default.
        $selectedYear = (int) ($this->filter ?? $this->getDefaultFilter());

        // Fetch ticket counts from the database, grouped by month.
        $ticketCountsByMonth = DB::table('tickets')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Prepare an array for all 12 months, initialized to 0.
        $monthlyData = array_fill(1, 12, 0);

        // Populate the array with the actual ticket counts.
        foreach ($ticketCountsByMonth as $month => $data) {
            $monthlyData[$month] = $data->count;
        }

        // Get the final array of counts for the chart.
        $chartData = array_values($monthlyData);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Total Tickets',
                    'data' => $chartData,
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
            'colors' => ['#0e2f66'],
        ];
    }
}
