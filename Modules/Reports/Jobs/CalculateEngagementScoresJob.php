<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Services\EngagementService;

class CalculateEngagementScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenantId;

    public function __construct(?int $tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    public function handle(EngagementService $engagementService)
    {
        $engagementService->updateAllScores($this->tenantId);
    }
}
