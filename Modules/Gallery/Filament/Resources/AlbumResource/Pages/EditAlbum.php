<?php

namespace Modules\Gallery\Filament\Resources\AlbumResource\Pages;

use Modules\Gallery\Filament\Resources\AlbumResource;
use Filament\Resources\Pages\EditRecord;

class EditAlbum extends EditRecord
{
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
