<?php

namespace Modules\Gallery\Filament\Resources\MediaCommentResource\Pages;

use Modules\Gallery\Filament\Resources\MediaCommentResource;
use Filament\Resources\Pages\ListRecords;

class ListMediaComments extends ListRecords
{
    protected static string $resource = MediaCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
