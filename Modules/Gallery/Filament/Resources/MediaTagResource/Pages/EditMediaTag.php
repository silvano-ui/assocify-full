<?php

namespace Modules\Gallery\Filament\Resources\MediaTagResource\Pages;

use Modules\Gallery\Filament\Resources\MediaTagResource;
use Filament\Resources\Pages\EditRecord;

class EditMediaTag extends EditRecord
{
    protected static string $resource = MediaTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
