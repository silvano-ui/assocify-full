<?php

namespace Modules\Localization\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Localization\Entities\TranslationSetting;

class ProviderUsageWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $settings = TranslationSetting::where('is_active', true)->get();
        $stats = [];

        foreach ($settings as $setting) {
            $limit = $setting->monthly_char_limit;
            $used = $setting->chars_used_this_month;
            $remaining = $limit ? ($limit - $used) : 'Unlimited';
            
            $stats[] = Stat::make(ucfirst($setting->provider) . ' Usage', number_format($used))
                ->description($limit ? "Limit: " . number_format($limit) : 'Unlimited')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($limit && $used > $limit * 0.9 ? 'danger' : 'success');
        }

        if (empty($stats)) {
            $stats[] = Stat::make('Providers', 'No active providers');
        }

        return $stats;
    }
}
