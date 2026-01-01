<?php

namespace Modules\Payments\Filament\Resources\SubscriptionRenewalResource\Pages;

use Modules\Payments\Filament\Resources\SubscriptionRenewalResource;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionRenewal extends EditRecord
{
    protected static string $resource = SubscriptionRenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
