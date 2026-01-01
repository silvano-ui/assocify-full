<?php

namespace Modules\Gallery\Filament\Resources\MediaCommentResource\Pages;

use Modules\Gallery\Filament\Resources\MediaCommentResource;
use Filament\Resources\Pages\EditRecord;

class EditMediaComment extends EditRecord
{
    protected static string $resource = MediaCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
