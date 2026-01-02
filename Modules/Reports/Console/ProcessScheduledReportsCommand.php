<?php

namespace Modules\Reports\Console;

use Illuminate\Console\Command;
use Modules\Reports\Services\SchedulerService;

class ProcessScheduledReportsCommand extends Command
{
    protected $signature = 'reports:process-scheduled';
    protected $description = 'Process due scheduled reports';

    public function handle(SchedulerService $schedulerService)
    {
        $this->info('Processing scheduled reports...');
        $schedulerService->processDueReports();
        $this->info('Done.');
    }
}
