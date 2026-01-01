<?php

namespace App\Filament\Admin\Resources\TenantFeatureResource\Pages;

use App\Filament\Admin\Resources\TenantFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantFeatures extends ListRecords
{
    protected static string $resource = TenantFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
