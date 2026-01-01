<?php

namespace Modules\Chat\Filament\Resources\ChatHashtagResource\Pages;

use Modules\Chat\Filament\Resources\ChatHashtagResource;
use Filament\Resources\Pages\EditRecord;

class EditChatHashtag extends EditRecord
{
    protected static string $resource = ChatHashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
