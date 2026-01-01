<?php

namespace Modules\Api\Filament\Resources\ApiRequestLogResource\Pages;

use Modules\Api\Filament\Resources\ApiRequestLogResource;
use Filament\Resources\Pages\ListRecords;

class ListApiRequestLogs extends ListRecords
{
    protected static string $resource = ApiRequestLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
