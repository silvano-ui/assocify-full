<?php

namespace Modules\Gallery\Filament\Resources\CustomGroupResource\Pages;

use Modules\Gallery\Filament\Resources\CustomGroupResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomGroup extends EditRecord
{
    protected static string $resource = CustomGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
