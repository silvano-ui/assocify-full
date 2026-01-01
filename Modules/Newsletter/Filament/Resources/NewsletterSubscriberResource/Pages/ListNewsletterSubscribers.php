<?php

namespace Modules\Newsletter\Filament\Resources\NewsletterSubscriberResource\Pages;

use Modules\Newsletter\Filament\Resources\NewsletterSubscriberResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNewsletterSubscribers extends ListRecords
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
