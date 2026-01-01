<?php

namespace Modules\Gallery\Filament\Resources\MediaResource\Pages;

use Modules\Gallery\Filament\Resources\MediaResource;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
