<?php

namespace Modules\Newsletter\Filament\Resources\NewsletterCampaignResource\Pages;

use Modules\Newsletter\Filament\Resources\NewsletterCampaignResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNewsletterCampaigns extends ListRecords
{
    protected static string $resource = NewsletterCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
