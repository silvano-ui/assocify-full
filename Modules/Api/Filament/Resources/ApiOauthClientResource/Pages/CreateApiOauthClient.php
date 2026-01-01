<?php

namespace Modules\Api\Filament\Resources\ApiOauthClientResource\Pages;

use Modules\Api\Filament\Resources\ApiOauthClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateApiOauthClient extends CreateRecord
{
    protected static string $resource = ApiOauthClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['client_secret_input'])) {
            $data['client_secret_hash'] = hash('sha256', $data['client_secret_input']);
            unset($data['client_secret_input']);
        } else {
             // Fallback if not set (should not happen with required)
             $secret = Str::random(40);
             $data['client_secret_hash'] = hash('sha256', $secret);
        }

        return $data;
    }
}
