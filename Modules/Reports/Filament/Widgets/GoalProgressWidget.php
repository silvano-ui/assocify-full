<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GoalProgressWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Members Goal', '80%')
                ->description('80/100'),
            Stat::make('Revenue Goal', '65%')
                ->description('6500/10000'),
        ];
    }

    public function getHeading(): string
    {
        return 'Goal Progress';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
