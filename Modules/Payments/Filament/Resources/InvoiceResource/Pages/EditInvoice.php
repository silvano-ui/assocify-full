<?php

namespace Modules\Payments\Filament\Resources\InvoiceResource\Pages;

use Modules\Payments\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
