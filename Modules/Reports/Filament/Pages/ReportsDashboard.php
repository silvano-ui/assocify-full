<?php

namespace Modules\Reports\Filament\Pages;

use Filament\Pages\Page;
use Modules\Reports\Filament\Widgets\MembersStatsWidget;
use Modules\Reports\Filament\Widgets\PaymentsStatsWidget;
use Modules\Reports\Filament\Widgets\EventsStatsWidget;
use Modules\Reports\Filament\Widgets\RevenueChartWidget;
use Modules\Reports\Filament\Widgets\MembersGrowthChartWidget;

class ReportsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'reports::filament.pages.reports-dashboard';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected function getHeaderWidgets(): array
    {
        return [
            MembersStatsWidget::class,
            PaymentsStatsWidget::class,
            EventsStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RevenueChartWidget::class,
            MembersGrowthChartWidget::class,
        ];
    }
}
