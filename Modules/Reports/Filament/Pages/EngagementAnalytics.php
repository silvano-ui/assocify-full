<?php

namespace Modules\Reports\Filament\Pages;

use Filament\Pages\Page;
use Modules\Reports\Filament\Widgets\EngagementOverviewWidget;

class EngagementAnalytics extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected string $view = 'reports::filament.pages.engagement-analytics';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected function getHeaderWidgets(): array
    {
        return [
            EngagementOverviewWidget::class,
        ];
    }
}
