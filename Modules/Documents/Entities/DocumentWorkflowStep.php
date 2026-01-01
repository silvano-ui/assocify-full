<?php

namespace Modules\Documents\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentWorkflowStep extends Model
{
    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(DocumentWorkflow::class, 'workflow_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
