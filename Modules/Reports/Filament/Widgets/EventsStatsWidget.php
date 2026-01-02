<?php

namespace Modules\Reports\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Upcoming Events', 3),
            Stat::make('Events This Month', 5),
            Stat::make('Registrations', 45),
        ];
    }

    public function getHeading(): string
    {
        return 'Events Statistics';
    }

    public function getPollingInterval(): ?string
    {
        return '15s';
    }
}
