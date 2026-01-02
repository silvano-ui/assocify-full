<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ReportGoal;

class GoalService
{
    public function updateProgress(ReportGoal $goal): void
    {
        // Recalculate current value based on goal metric
        // $goal->current_value = ...
        $goal->save();
        $this->checkAchievement($goal);
    }

    public function checkAchievement(ReportGoal $goal): void
    {
        if ($goal->isAchieved() && !$goal->is_achieved) { // Check if just achieved
             // Notify
        }
    }
}
