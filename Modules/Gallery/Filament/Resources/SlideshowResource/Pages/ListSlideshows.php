<?php

namespace Modules\Gallery\Filament\Resources\SlideshowResource\Pages;

use Modules\Gallery\Filament\Resources\SlideshowResource;
use Filament\Resources\Pages\ListRecords;

class ListSlideshows extends ListRecords
{
    protected static string $resource = SlideshowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
