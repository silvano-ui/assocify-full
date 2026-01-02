<?php

namespace Modules\Localization\Filament\Resources\TenantTranslationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Localization\Filament\Resources\TenantTranslationResource;

class ListTenantTranslations extends ListRecords
{
    protected static string $resource = TenantTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
