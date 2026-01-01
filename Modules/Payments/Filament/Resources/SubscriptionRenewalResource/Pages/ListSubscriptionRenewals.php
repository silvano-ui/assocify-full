<?php

namespace Modules\Payments\Filament\Resources\SubscriptionRenewalResource\Pages;

use Modules\Payments\Filament\Resources\SubscriptionRenewalResource;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionRenewals extends ListRecords
{
    protected static string $resource = SubscriptionRenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
