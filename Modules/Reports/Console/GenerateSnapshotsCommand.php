<?php

namespace Modules\Reports\Console;

use Illuminate\Console\Command;
use Modules\Reports\Services\SnapshotService;
use Modules\Reports\Entities\ReportTemplate;

class GenerateSnapshotsCommand extends Command
{
    protected $signature = 'reports:snapshots';
    protected $description = 'Generate monthly snapshots for reports';

    public function handle(SnapshotService $snapshotService)
    {
        $this->info('Generating snapshots...');
        
        $templates = ReportTemplate::where('is_active', true)->get();
        $period = now()->subMonth()->format('Y-m'); // Previous month

        foreach ($templates as $template) {
            $snapshotService->createSnapshot($template, $period);
        }
        
        $this->info('Done.');
    }
}
