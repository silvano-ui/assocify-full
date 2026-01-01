<?php

namespace Modules\Chat\Filament\Resources\MessageResource\Pages;

use Modules\Chat\Filament\Resources\MessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMessage extends CreateRecord
{
    protected static string $resource = MessageResource::class;
}
