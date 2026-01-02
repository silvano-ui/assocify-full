<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\GeneratedReport;
use Modules\Reports\Entities\ReportShare;

class ShareService
{
    public function createShare(GeneratedReport $report, array $options = []): ReportShare
    {
        return ReportShare::create([
            'generated_report_id' => $report->id,
            'is_active' => true,
            'expires_at' => $options['expires_at'] ?? now()->addDays(7),
        ]);
    }

    public function validateToken(string $token): ?ReportShare
    {
        $share = ReportShare::where('token', $token)->first();

        if ($share && $share->isValid()) {
            return $share;
        }

        return null;
    }
}
