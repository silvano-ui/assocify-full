<?php

namespace Modules\Events\Filament\Resources\EventResource\Pages;

use Modules\Events\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
