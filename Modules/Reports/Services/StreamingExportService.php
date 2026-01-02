<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ReportTemplate;
use Illuminate\Support\Facades\Storage;

class StreamingExportService
{
    public function export(ReportTemplate $template, string $path): void
    {
        $handle = fopen(Storage::disk('reports')->path($path), 'w');

        // Generator for fetching data in chunks
        $dataGenerator = $this->getDataGenerator($template);

        foreach ($dataGenerator as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }

    protected function getDataGenerator(ReportTemplate $template)
    {
        // Pseudo implementation
        // Real implementation would use QueryBuilder chunking
        $query = (new DataSourceService)->getQueryBuilder($template);
        
        foreach ($query->cursor() as $record) {
            yield $record->toArray();
        }
    }
}
