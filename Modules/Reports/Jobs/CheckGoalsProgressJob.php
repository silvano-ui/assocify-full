<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Services\GoalService;
use Modules\Reports\Entities\ReportGoal;

class CheckGoalsProgressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GoalService $goalService)
    {
        $goals = ReportGoal::where('is_active', true)
            ->whereDate('end_date', '>=', now())
            ->get();

        foreach ($goals as $goal) {
            $goalService->updateProgress($goal);
            $goalService->checkAchievement($goal);
        }
    }
}
