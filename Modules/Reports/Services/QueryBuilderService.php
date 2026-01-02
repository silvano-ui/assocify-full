<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ReportTemplate;
use Illuminate\Database\Eloquent\Builder;

class QueryBuilderService
{
    protected DataSourceService $dataSourceService;

    public function __construct(DataSourceService $dataSourceService)
    {
        $this->dataSourceService = $dataSourceService;
    }

    public function buildQuery(ReportTemplate $template, array $filters = [], array $sorting = []): Builder
    {
        $query = $this->dataSourceService->getQueryBuilder($template);
        
        $this->applyFilters($query, $filters);
        $this->applySorting($query, $sorting);
        
        return $query;
    }

    public function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $filter) {
            // Basic filter implementation
            if (isset($filter['field']) && isset($filter['operator']) && isset($filter['value'])) {
                $field = $filter['field'];
                $operator = $filter['operator'];
                $value = $filter['value'];

                if (str_contains($field, '.')) {
                    // Handle relationship filtering
                    [$relation, $relationField] = explode('.', $field, 2);
                    $query->whereHas($relation, function ($q) use ($relationField, $operator, $value) {
                        $q->where($relationField, $operator, $value);
                    });
                } else {
                    $query->where($field, $operator, $value);
                }
            }
        }
    }

    public function applySorting(Builder $query, array $sorting): void
    {
        if (isset($sorting['field']) && isset($sorting['direction'])) {
            $query->orderBy($sorting['field'], $sorting['direction']);
        }
    }

    public function executeQuery(Builder $query): array
    {
        return $query->get()->toArray();
    }
}
