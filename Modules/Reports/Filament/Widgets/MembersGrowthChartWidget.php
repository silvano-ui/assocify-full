<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MembersGrowthChartWidget extends ChartWidget
{
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Members',
                    'data' => [100, 105, 110, 115, 120, 130, 135, 140, 145, 150, 155, 160],
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string
    {
        return 'Members Growth - Last 12 Months';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
