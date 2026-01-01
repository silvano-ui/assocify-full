<?php

namespace Modules\Newsletter\Filament\Resources\NewsletterListResource\Pages;

use Modules\Newsletter\Filament\Resources\NewsletterListResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNewsletterLists extends ListRecords
{
    protected static string $resource = NewsletterListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
