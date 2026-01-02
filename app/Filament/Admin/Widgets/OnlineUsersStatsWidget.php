<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OnlineUsersStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 2;

    protected function getStats(): array
    {
        $onlineCount = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->distinct('user_id')
            ->count('user_id');

        return [
            Stat::make('Online Users', $onlineCount)
                ->description('Active in the last ' . config('session.lifetime') . ' minutes')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}
