<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodResource;
use Filament\Resources\Pages\EditRecord;

class EditPaymentMethod extends EditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
