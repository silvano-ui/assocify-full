<?php

namespace Modules\Api\Filament\Pages;

use Filament\Pages\Page;

class ApiAnalyticsPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'api::filament.pages.api-analytics';

    protected static string | \UnitEnum | null $navigationGroup = 'API';

    protected static ?string $title = 'API Analytics';
}
