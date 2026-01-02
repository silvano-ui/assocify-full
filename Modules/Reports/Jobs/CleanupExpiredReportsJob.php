<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Entities\GeneratedReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $retentionDays = config('reports.retention_days', 90);
        $expiredDate = Carbon::now()->subDays($retentionDays);

        $reports = GeneratedReport::where('created_at', '<', $expiredDate)->get();

        foreach ($reports as $report) {
            // Observer handles file deletion, but we can double check or trigger delete
            $report->delete();
        }
    }
}
