<?php

namespace Modules\WhiteLabel\Filament\Resources\TenantBrandingResource\Pages;

use Modules\WhiteLabel\Filament\Resources\TenantBrandingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantBrandings extends ListRecords
{
    protected static string $resource = TenantBrandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
