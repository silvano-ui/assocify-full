<?php

namespace Modules\Reports\Console;

use Illuminate\Console\Command;
use Modules\Reports\Jobs\CheckGoalsProgressJob;

class CheckGoalsCommand extends Command
{
    protected $signature = 'reports:goals';
    protected $description = 'Check progress for active goals';

    public function handle()
    {
        $this->info('Checking goals progress...');
        CheckGoalsProgressJob::dispatch();
        $this->info('Done.');
    }
}
