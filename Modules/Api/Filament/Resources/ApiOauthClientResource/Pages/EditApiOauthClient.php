<?php

namespace Modules\Api\Filament\Resources\ApiOauthClientResource\Pages;

use Modules\Api\Filament\Resources\ApiOauthClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiOauthClient extends EditRecord
{
    protected static string $resource = ApiOauthClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
