<?php

namespace Modules\Gallery\Filament\Resources\MediaTagResource\Pages;

use Modules\Gallery\Filament\Resources\MediaTagResource;
use Filament\Resources\Pages\ListRecords;

class ListMediaTags extends ListRecords
{
    protected static string $resource = MediaTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
