<?php

namespace Modules\Api\Filament\Resources\ApiWebhookResource\Pages;

use Modules\Api\Filament\Resources\ApiWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiWebhook extends EditRecord
{
    protected static string $resource = ApiWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
