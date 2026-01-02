<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ReportTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

class DataSourceService
{
    public function getDataSourceClass(string $dataSource): ?string
    {
        $sources = Config::get('reports.data_sources', []);
        return $sources[$dataSource] ?? null;
    }

    public function getQueryBuilder(ReportTemplate $template): Builder
    {
        $modelClass = $this->getDataSourceClass($template->data_source);
        
        if (!$modelClass || !class_exists($modelClass)) {
            throw new \Exception("Invalid data source: {$template->data_source}");
        }

        return $modelClass::query();
    }

    public function getAvailableColumns(string $dataSource): array
    {
        $modelClass = $this->getDataSourceClass($dataSource);
        
        if (!$modelClass) {
            return [];
        }

        // Basic implementation: get columns from table schema or model fillable
        // This is a simplified version
        $model = new $modelClass;
        $table = $model->getTable();
        
        return \Illuminate\Support\Facades\Schema::getColumnListing($table);
    }

    public function getAvailableFilters(string $dataSource): array
    {
        // Return filterable fields
        return $this->getAvailableColumns($dataSource);
    }
}
