<?php

namespace Modules\Reports\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Reports\Filament\Resources\ReportTemplateResource;
use Modules\Reports\Filament\Resources\GeneratedReportResource;
use Modules\Reports\Filament\Resources\ScheduledReportResource;
use Modules\Reports\Filament\Pages\ReportsDashboard;
use Modules\Reports\Filament\Pages\ReportBuilder;
use Modules\Reports\Filament\Pages\GoalsDashboard;
use Modules\Reports\Filament\Pages\EngagementAnalytics;
use Modules\Reports\Filament\Pages\FiscalReports;
use Modules\Reports\Filament\Widgets\MembersStatsWidget;
use Modules\Reports\Filament\Widgets\PaymentsStatsWidget;
use Modules\Reports\Filament\Widgets\EventsStatsWidget;
use Modules\Reports\Filament\Widgets\RevenueChartWidget;
use Modules\Reports\Filament\Widgets\MembersGrowthChartWidget;
use Modules\Reports\Filament\Widgets\EngagementOverviewWidget;
use Modules\Reports\Filament\Widgets\GoalProgressWidget;
use Modules\Reports\Filament\Admin\Resources\AdminReportTemplateResource;
use Modules\Reports\Filament\Admin\Resources\AdminGeneratedReportResource;
use Modules\Reports\Filament\Admin\Resources\AdminScheduledReportResource;
use Modules\Reports\Filament\Admin\Widgets\GlobalStatsWidget;
use Modules\Reports\Filament\Admin\Widgets\TenantsComparisonWidget;
use Modules\Reports\Filament\Admin\Widgets\PlatformRevenueWidget;

class ReportsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'reports';
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() === 'admin') {
            $panel->resources([
                AdminReportTemplateResource::class,
                AdminGeneratedReportResource::class,
                AdminScheduledReportResource::class,
            ])
            ->widgets([
                GlobalStatsWidget::class,
                TenantsComparisonWidget::class,
                PlatformRevenueWidget::class,
            ]);
        } else {
            $panel->resources([
                ReportTemplateResource::class,
                GeneratedReportResource::class,
                ScheduledReportResource::class,
            ])
            ->pages([
                ReportsDashboard::class,
                ReportBuilder::class,
                GoalsDashboard::class,
                EngagementAnalytics::class,
                FiscalReports::class,
            ])
            ->widgets([
                MembersStatsWidget::class,
                PaymentsStatsWidget::class,
                EventsStatsWidget::class,
                RevenueChartWidget::class,
                MembersGrowthChartWidget::class,
                EngagementOverviewWidget::class,
                GoalProgressWidget::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }
}
