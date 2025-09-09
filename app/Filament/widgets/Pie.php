<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
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

    // protected int | string | array $columnSpan = 'full';

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
        // 1. Get counts for each status_id directly from the tickets table
        // This will return a collection like: [{ status_id: 1, count: 5 }, { status_id: 2, count: 10 }]
        $ticketCounts = Ticket::query()
            ->selectRaw('status_id, count(*) as count')
            ->groupBy('status_id')
            ->get()
            ->keyBy('status_id'); // Key the collection by status_id for easy lookup

        $seriesData = [];
        $labelsData = [];
        $colorsData = [];

        // 2. Define a mapping of Status ID to a specific color
        // IMPORTANT: Customize these colors to match your desired scheme for each status.
        $statusColors = [
            Ticket::STATUS_PENDING => '#e38b07',  // Example: Orange for Pending
            Ticket::STATUS_RESOLVED => '#00e396', // Example: Green for Resolved
            Ticket::STATUS_UNASSIGNED => '#808080',// Example: Gray for Unassigned
            Ticket::STATUS_REOPENED => '#0e9ce8', // Example: Blue for Reopened
        ];

        // 3. Iterate through all defined statuses in your Ticket model
        // and collect the data for the chart.
        // Ensure you are using Ticket::STATUS_NAMES (or whatever your ID-to-name array is called)
        foreach (Ticket::STATUSES as $statusId => $statusName) {
            $count = $ticketCounts->get($statusId)->count ?? 0; // Get count, default to 0 if no tickets for this status
            $seriesData[] = $count;
            $labelsData[] = $statusName;
            // Use the defined specific color for the status, or a default if not mapped
            $colorsData[] = $statusColors[$statusId] ?? '#cccccc';
        }

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
            ],
            'series' => $seriesData,
            'labels' => $labelsData,
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
            'colors' => $colorsData,
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isAgent();
    }

}
