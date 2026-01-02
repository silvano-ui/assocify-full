<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ReportTemplate;
use Modules\Reports\Entities\GeneratedReport;
use Modules\Reports\Jobs\ProcessReportJob;

class ExportService
{
    public function generate(ReportTemplate $template, string $format, array $options = []): GeneratedReport
    {
        $report = GeneratedReport::create([
            'template_id' => $template->id,
            'format' => $format,
            'status' => 'pending', // Assuming status column exists or we use incomplete completed_at
            'options' => $options,
        ]);

        // Check estimated row count if possible, or just dispatch
        // For simplicity, let's assume we can count quickly
        $count = 0; // $this->queryBuilder->buildQuery($template)->count();
        
        if ($count > 10000) {
            // Async
            // dispatch(new ProcessReportJob($report));
        } else {
            // Sync
            $this->processReport($report);
        }

        return $report;
    }

    public function processReport(GeneratedReport $report): void
    {
        // Logic to fetch data and call PdfExportService or ExcelExportService
        $report->update(['completed_at' => now()]);
    }
}
