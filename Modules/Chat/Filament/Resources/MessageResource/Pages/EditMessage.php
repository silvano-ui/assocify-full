<?php

namespace Modules\Chat\Filament\Resources\MessageResource\Pages;

use Modules\Chat\Filament\Resources\MessageResource;
use Filament\Resources\Pages\EditRecord;

class EditMessage extends EditRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
