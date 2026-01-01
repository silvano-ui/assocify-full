<?php

namespace Modules\Newsletter\Filament\Resources\NewsletterTemplateResource\Pages;

use Modules\Newsletter\Filament\Resources\NewsletterTemplateResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNewsletterTemplates extends ListRecords
{
    protected static string $resource = NewsletterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
