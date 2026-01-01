<?php

namespace Modules\Api\Filament\Pages;

use Filament\Pages\Page;

class ApiDocumentationPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected string $view = 'api::filament.pages.api-documentation';

    protected static string | \UnitEnum | null $navigationGroup = 'API';

    protected static ?string $title = 'API Documentation';
}
