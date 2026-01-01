<?php

namespace Modules\Events\Filament\Resources\EventCategoryResource\Pages;

use Modules\Events\Filament\Resources\EventCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventCategories extends ListRecords
{
    protected static string $resource = EventCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
