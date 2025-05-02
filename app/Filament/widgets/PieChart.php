<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PieChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '250px';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [10, 20, 30],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                    'borderAligned' => 'inner',
                ],
            ],
            'labels' => ['Red', 'Blue', 'Yellow'],

        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected static ?array $options = [
    'plugins' => [
        'legend' => [
            'display' => true,
            'position' => 'right',
        ],
    ],
];
}
