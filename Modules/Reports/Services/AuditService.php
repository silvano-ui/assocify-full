<?php

namespace Modules\Reports\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Reports\Entities\ReportAuditLog;

class AuditService
{
    public function log(Model $model, string $action, array $metadata = []): void
    {
        ReportAuditLog::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'action' => $action, // e.g., 'viewed', 'exported'
            'user_id' => auth()->id(),
            'metadata' => $metadata,
        ]);
    }

    public function getAuditTrail(Model $model)
    {
        return ReportAuditLog::where('auditable_type', get_class($model))
                             ->where('auditable_id', $model->id)
                             ->orderByDesc('created_at')
                             ->get();
    }
}
