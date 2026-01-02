<?php

namespace Modules\Localization\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;

class TranslationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Keys', Translation::distinct('key')->count()),
            Stat::make('Active Languages', Language::where('is_active', true)->count()),
            Stat::make('Auto Translated', Translation::where('is_auto_translated', true)->count()),
        ];
    }
}
