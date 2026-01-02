<?php

namespace Modules\Reports\Console;

use Illuminate\Console\Command;
use Modules\Reports\Jobs\CalculateEngagementScoresJob;

class CalculateEngagementCommand extends Command
{
    protected $signature = 'reports:engagement';
    protected $description = 'Calculate engagement scores for all members';

    public function handle()
    {
        $this->info('Calculating engagement scores...');
        CalculateEngagementScoresJob::dispatch();
        $this->info('Done.');
    }
}
