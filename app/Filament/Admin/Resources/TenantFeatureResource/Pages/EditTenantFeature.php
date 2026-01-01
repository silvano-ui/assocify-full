<?php

namespace App\Filament\Admin\Resources\TenantFeatureResource\Pages;

use App\Filament\Admin\Resources\TenantFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantFeature extends EditRecord
{
    protected static string $resource = TenantFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
