<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', '€ 12,500'),
            Stat::make('Pending', '€ 1,200'),
            Stat::make('Renewals', 15),
        ];
    }

    public function getHeading(): string
    {
        return 'Payments Statistics';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
