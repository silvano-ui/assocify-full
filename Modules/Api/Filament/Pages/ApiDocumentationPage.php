<?php

namespace Modules\Api\Filament\Pages;

use Filament\Pages\Page;

class ApiDocumentationPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected string $view = 'api::filament.pages.api-documentation';

    protected static string | \UnitEnum | null $navigationGroup = 'API';

    protected static ?string $title = 'API Documentation';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // SuperAdmin always has access
        if (!$user->tenant_id) return true;
        
        // Tenant users need api.access feature
        return function_exists('has_feature') && has_feature('api.access');
    }


}
