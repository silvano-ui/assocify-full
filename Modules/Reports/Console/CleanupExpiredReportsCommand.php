<?php

namespace Modules\Reports\Console;

use Illuminate\Console\Command;
use Modules\Reports\Jobs\CleanupExpiredReportsJob;

class CleanupExpiredReportsCommand extends Command
{
    protected $signature = 'reports:cleanup';
    protected $description = 'Cleanup expired generated reports';

    public function handle()
    {
        $this->info('Cleaning up expired reports...');
        CleanupExpiredReportsJob::dispatch();
        $this->info('Done.');
    }
}
