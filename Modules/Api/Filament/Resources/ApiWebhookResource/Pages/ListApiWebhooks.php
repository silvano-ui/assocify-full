<?php

namespace Modules\Api\Filament\Resources\ApiWebhookResource\Pages;

use Modules\Api\Filament\Resources\ApiWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiWebhooks extends ListRecords
{
    protected static string $resource = ApiWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
