<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;
}
