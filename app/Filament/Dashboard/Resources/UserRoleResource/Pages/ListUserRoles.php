<?php

namespace App\Filament\Dashboard\Resources\UserRoleResource\Pages;

use App\Filament\Dashboard\Resources\UserRoleResource;
use Filament\Resources\Pages\ListRecords;

class ListUserRoles extends ListRecords
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create user here, usually done in Member/User management
        ];
    }
}
