<?php

namespace Modules\Payments\Filament\Resources\TransactionResource\Pages;

use Modules\Payments\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
