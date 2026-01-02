<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Modules\Localization\Jobs\SyncTranslationsJob;

class SyncTranslationsCommand extends Command
{
    protected $signature = 'localization:sync';
    protected $description = 'Sync translations from language files to database';

    public function handle()
    {
        $this->info('Starting synchronization...');
        SyncTranslationsJob::dispatch();
        $this->info('Synchronization job dispatched.');
        return 0;
    }
}
