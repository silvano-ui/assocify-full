<?php

namespace Modules\Documents\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentWorkflow extends Model
{
    protected $guarded = [];

    protected $casts = [
        'steps' => 'array',
        'is_template' => 'boolean',
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (!$model->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
        });

        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workflowSteps(): HasMany
    {
        return $this->hasMany(DocumentWorkflowStep::class, 'workflow_id');
    }
}
