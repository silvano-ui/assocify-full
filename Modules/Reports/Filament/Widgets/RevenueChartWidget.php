<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [1000, 1200, 1500, 1300, 1600, 1800, 2000, 2200, 2100, 2400, 2600, 3000],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string
    {
        return 'Revenue - Last 12 Months';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
