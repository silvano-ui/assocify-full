<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Entities\ReportTemplate;
use Modules\Reports\Services\SnapshotService;

class GenerateSnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $template;
    protected $period;

    public function __construct(ReportTemplate $template, string $period)
    {
        $this->template = $template;
        $this->period = $period;
    }

    public function handle(SnapshotService $snapshotService)
    {
        $snapshotService->createSnapshot($this->template, $this->period);
    }
}
