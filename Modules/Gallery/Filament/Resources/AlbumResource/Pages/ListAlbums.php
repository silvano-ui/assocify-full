<?php

namespace Modules\Gallery\Filament\Resources\AlbumResource\Pages;

use Modules\Gallery\Filament\Resources\AlbumResource;
use Filament\Resources\Pages\ListRecords;

class ListAlbums extends ListRecords
{
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
