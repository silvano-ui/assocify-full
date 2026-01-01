<?php

namespace App\Filament\Admin\Resources\RoleTemplateResource\Pages;

use App\Filament\Admin\Resources\RoleTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoleTemplate extends EditRecord
{
    protected static string $resource = RoleTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
