<?php

namespace Modules\Gallery\Filament\Resources\CollectionResource\Pages;

use Modules\Gallery\Filament\Resources\CollectionResource;
use Filament\Resources\Pages\ListRecords;

class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
