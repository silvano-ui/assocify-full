<?php

namespace Modules\Reports\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class PlatformRevenueWidget extends ChartWidget
{
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [10000, 12000, 15000, 18000, 20000, 25000, 30000, 35000, 40000, 45000, 50000, 60000],
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
        return 'Platform Revenue Growth';
    }

    public function getPollingInterval(): ?string
    {
        return '60s';
    }
}
