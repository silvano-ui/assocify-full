<?php

namespace Modules\Reports\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class TenantsComparisonWidget extends ChartWidget
{
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [5000, 4500, 4000, 3500, 3000, 2500, 2000, 1500, 1000, 500],
                ],
            ],
            'labels' => ['Tenant A', 'Tenant B', 'Tenant C', 'Tenant D', 'Tenant E', 'Tenant F', 'Tenant G', 'Tenant H', 'Tenant I', 'Tenant J'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string
    {
        return 'Top 10 Tenants by Revenue';
    }

    public function getPollingInterval(): ?string
    {
        return '60s';
    }
}
