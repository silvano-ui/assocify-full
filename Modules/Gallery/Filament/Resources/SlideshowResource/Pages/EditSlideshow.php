<?php

namespace Modules\Gallery\Filament\Resources\SlideshowResource\Pages;

use Modules\Gallery\Filament\Resources\SlideshowResource;
use Filament\Resources\Pages\EditRecord;

class EditSlideshow extends EditRecord
{
    protected static string $resource = SlideshowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
