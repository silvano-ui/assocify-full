<?php

namespace Modules\Chat\Filament\Resources\ConversationResource\Pages;

use Modules\Chat\Filament\Resources\ConversationResource;
use Filament\Resources\Pages\EditRecord;

class EditConversation extends EditRecord
{
    protected static string $resource = ConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
