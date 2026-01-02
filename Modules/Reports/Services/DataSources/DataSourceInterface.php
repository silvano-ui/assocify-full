<?php

namespace Modules\Reports\Services\DataSources;

use Illuminate\Database\Eloquent\Builder;

interface DataSourceInterface
{
    public function getQuery(): Builder;
    
    public function getLabel(): string;
    
    public function getAvailableColumns(): array;
    
    public function getFilterableColumns(): array;
}
