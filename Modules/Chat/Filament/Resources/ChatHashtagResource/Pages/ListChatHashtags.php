<?php

namespace Modules\Chat\Filament\Resources\ChatHashtagResource\Pages;

use Modules\Chat\Filament\Resources\ChatHashtagResource;
use Filament\Resources\Pages\ListRecords;

class ListChatHashtags extends ListRecords
{
    protected static string $resource = ChatHashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
