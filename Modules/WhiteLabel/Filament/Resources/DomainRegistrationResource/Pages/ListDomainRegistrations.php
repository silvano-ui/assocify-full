<?php

namespace Modules\WhiteLabel\Filament\Resources\DomainRegistrationResource\Pages;

use Modules\WhiteLabel\Filament\Resources\DomainRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDomainRegistrations extends ListRecords
{
    protected static string $resource = DomainRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
