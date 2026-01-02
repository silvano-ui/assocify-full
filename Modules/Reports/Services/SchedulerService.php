<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ScheduledReport;

class SchedulerService
{
    public function processDueReports(): void
    {
        $dueReports = ScheduledReport::due()->get();

        foreach ($dueReports as $report) {
            // Check trigger conditions if any
            if ($this->checkTriggerConditions($report)) {
                // Dispatch job or process
                // For now, mark as run
                $report->markAsRun();
            }
        }
    }

    protected function checkTriggerConditions(ScheduledReport $report): bool
    {
        // Check if logic matches (e.g., "new members > 0")
        return true;
    }
}
