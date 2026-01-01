<?php

namespace Modules\Gallery\Filament\Resources\CollectionResource\Pages;

use Modules\Gallery\Filament\Resources\CollectionResource;
use Filament\Resources\Pages\EditRecord;

class EditCollection extends EditRecord
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
