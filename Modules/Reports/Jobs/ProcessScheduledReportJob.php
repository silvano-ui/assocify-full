<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Entities\ScheduledReport;
use Modules\Reports\Services\ExportService;

class ProcessScheduledReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scheduledReport;

    public function __construct(ScheduledReport $scheduledReport)
    {
        $this->scheduledReport = $scheduledReport;
    }

    public function handle(ExportService $exportService)
    {
        try {
            $template = $this->scheduledReport->template;
            $options = ['format' => 'xlsx']; // Default or from scheduled report settings

            // Generate report
            $generatedReport = $exportService->generate($template, $options['format'], $options);
            
            // Link to scheduled report
            $generatedReport->update(['scheduled_report_id' => $this->scheduledReport->id]);
            
            // Mark as run
            $this->scheduledReport->markAsRun();

            // Dispatch notification job
            SendReportNotificationJob::dispatch($generatedReport, $this->scheduledReport->recipients);

        } catch (\Exception $e) {
            $this->scheduledReport->markAsFailed($e->getMessage());
            throw $e;
        }
    }
}
