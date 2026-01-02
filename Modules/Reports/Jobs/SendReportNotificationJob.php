<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Entities\GeneratedReport;
use Illuminate\Support\Facades\Mail;
// use Modules\Reports\Emails\ReportReadyEmail; // Assuming email class exists or logic here

class SendReportNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;
    protected $recipients;

    public function __construct(GeneratedReport $report, array $recipients)
    {
        $this->report = $report;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        foreach ($this->recipients as $recipient) {
            // Mail::to($recipient)->send(new ReportReadyEmail($this->report));
            // Placeholder for email sending logic
        }
    }
}
