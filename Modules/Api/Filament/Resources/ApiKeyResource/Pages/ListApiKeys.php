<?php

namespace Modules\Api\Filament\Resources\ApiKeyResource\Pages;

use Modules\Api\Filament\Resources\ApiKeyResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListApiKeys extends ListRecords
{
    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
