<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Entities\GeneratedReport;
use Modules\Reports\Entities\ReportTemplate;
use Modules\Reports\Services\ExportService;

class ProcessReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $template;
    protected $options;
    protected $generatedReportId;

    public function __construct(ReportTemplate $template, array $options = [], ?int $generatedReportId = null)
    {
        $this->template = $template;
        $this->options = $options;
        $this->generatedReportId = $generatedReportId;
    }

    public function handle(ExportService $exportService)
    {
        $generatedReport = $this->generatedReportId 
            ? GeneratedReport::find($this->generatedReportId)
            : null;

        if ($generatedReport) {
            $generatedReport->update(['status' => 'processing']);
        }

        try {
            // Logic to process report using ExportService
            // This is a placeholder as the actual implementation depends on ExportService details
             if ($generatedReport) {
                 $exportService->processReport($generatedReport);
             }
            
        } catch (\Exception $e) {
            if ($generatedReport) {
                $generatedReport->update([
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ]);
            }
            throw $e;
        }
    }
}
