<?php

namespace Modules\Reports\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tenants', 50),
            Stat::make('Total Members', 5000),
            Stat::make('Total Revenue', '€ 250,000'),
        ];
    }

    public function getHeading(): string
    {
        return 'Platform Statistics';
    }

    public function getPollingInterval(): ?string
    {
        return '30s';
    }
}
