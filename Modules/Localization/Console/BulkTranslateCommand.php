<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Modules\Localization\Jobs\BulkTranslateJob;
use Modules\Localization\Entities\Translation;

class BulkTranslateCommand extends Command
{
    protected $signature = 'localization:translate {from} {to} {--provider=deepl}';
    protected $description = 'Bulk translate missing keys via CLI';

    public function handle()
    {
        $from = $this->argument('from');
        $to = $this->argument('to');
        $provider = $this->option('provider');

        $this->info("Starting bulk translation from {$from} to {$to} using {$provider}...");

        // Get all groups
        $groups = Translation::distinct()->pluck('group')->toArray();

        BulkTranslateJob::dispatch($from, [$to], $groups, $provider);

        $this->info('Bulk translation job dispatched.');
        return 0;
    }
}
