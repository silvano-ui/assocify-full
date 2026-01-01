<?php

namespace Modules\Api\Filament\Resources\ApiSecurityEventResource\Pages;

use Modules\Api\Filament\Resources\ApiSecurityEventResource;
use Filament\Resources\Pages\ListRecords;

class ListApiSecurityEvents extends ListRecords
{
    protected static string $resource = ApiSecurityEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
