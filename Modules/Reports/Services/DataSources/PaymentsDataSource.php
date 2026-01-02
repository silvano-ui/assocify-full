<?php

namespace Modules\Reports\Services\DataSources;

use Illuminate\Database\Eloquent\Builder;
use Modules\Payments\Entities\Transaction;
use Illuminate\Support\Facades\Schema;

class PaymentsDataSource implements DataSourceInterface
{
    public function getQuery(): Builder
    {
        return Transaction::query();
    }

    public function getLabel(): string
    {
        return 'Payments';
    }

    public function getAvailableColumns(): array
    {
        return [
            'id',
            'amount',
            'status',
            'type',
            'reference',
            'created_at',
            'updated_at',
        ];
    }

    public function getFilterableColumns(): array
    {
        return [
            'status',
            'type',
            'amount',
            'created_at',
        ];
    }
}
