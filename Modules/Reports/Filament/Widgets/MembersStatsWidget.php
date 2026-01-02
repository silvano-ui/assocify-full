<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MembersStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Members', 150),
            Stat::make('Active Members', 120),
            Stat::make('New This Month', 10),
        ];
    }

    public function getHeading(): string
    {
        return 'Members Statistics';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
