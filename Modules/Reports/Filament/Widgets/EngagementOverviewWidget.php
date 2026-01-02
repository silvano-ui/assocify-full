<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class EngagementOverviewWidget extends ChartWidget
{
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Members',
                    'data' => [30, 50, 20, 10, 5],
                    'backgroundColor' => ['#22c55e', '#3b82f6', '#eab308', '#f97316', '#ef4444'],
                ],
            ],
            'labels' => ['Highly Active', 'Active', 'Moderate', 'At Risk', 'Dormant'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): string
    {
        return 'Engagement Segments Distribution';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
