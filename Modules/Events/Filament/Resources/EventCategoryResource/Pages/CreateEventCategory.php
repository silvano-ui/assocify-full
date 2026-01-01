<?php

namespace Modules\Events\Filament\Resources\EventCategoryResource\Pages;

use Modules\Events\Filament\Resources\EventCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventCategory extends CreateRecord
{
    protected static string $resource = EventCategoryResource::class;
}
