<?php

namespace Modules\Newsletter\Filament\Resources\NewsletterAutomationResource\Pages;

use Modules\Newsletter\Filament\Resources\NewsletterAutomationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNewsletterAutomations extends ListRecords
{
    protected static string $resource = NewsletterAutomationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
