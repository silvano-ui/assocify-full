<?php

namespace Modules\Reports\Filament\Pages;

use Filament\Pages\Page;
use Modules\Reports\Filament\Widgets\GoalProgressWidget;

class GoalsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected string $view = 'reports::filament.pages.goals-dashboard';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected function getHeaderWidgets(): array
    {
        return [
            GoalProgressWidget::class,
        ];
    }
}
