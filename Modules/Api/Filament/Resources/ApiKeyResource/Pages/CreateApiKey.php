<?php

namespace Modules\Api\Filament\Resources\ApiKeyResource\Pages;

use Modules\Api\Filament\Resources\ApiKeyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;
}
