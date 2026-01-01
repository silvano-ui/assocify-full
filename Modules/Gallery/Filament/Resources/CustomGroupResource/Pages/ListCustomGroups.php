<?php

namespace Modules\Gallery\Filament\Resources\CustomGroupResource\Pages;

use Modules\Gallery\Filament\Resources\CustomGroupResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomGroups extends ListRecords
{
    protected static string $resource = CustomGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
