<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethods extends ListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
