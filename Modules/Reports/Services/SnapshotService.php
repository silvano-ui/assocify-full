<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\ReportTemplate;
use Modules\Reports\Entities\ReportSnapshot;
use Carbon\Carbon;

class SnapshotService
{
    public function createSnapshot(ReportTemplate $template, string $period = 'monthly'): ReportSnapshot
    {
        // Execute report logic
        // $data = ...
        $data = ['mock' => 'data']; // Placeholder

        return ReportSnapshot::create([
            'report_template_id' => $template->id,
            'snapshot_date' => now(),
            'data' => $data,
        ]);
    }
}
