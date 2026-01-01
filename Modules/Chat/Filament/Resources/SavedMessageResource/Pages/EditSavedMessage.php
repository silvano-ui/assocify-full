<?php

namespace Modules\Chat\Filament\Resources\SavedMessageResource\Pages;

use Modules\Chat\Filament\Resources\SavedMessageResource;
use Filament\Resources\Pages\EditRecord;

class EditSavedMessage extends EditRecord
{
    protected static string $resource = SavedMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
