<?php

namespace Modules\Chat\Filament\Resources\SavedMessageResource\Pages;

use Modules\Chat\Filament\Resources\SavedMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListSavedMessages extends ListRecords
{
    protected static string $resource = SavedMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
