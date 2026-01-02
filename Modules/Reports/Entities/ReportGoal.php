<?php

namespace Modules\Reports\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;

class ReportGoal extends Model
{
    protected $guarded = [];

    protected $casts = [
        'target_value' => 'float',
        'current_value' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_achieved' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getProgressPercentage(): float
    {
        if ($this->target_value <= 0) {
            return 0;
        }
        $percentage = ($this->current_value / $this->target_value) * 100;
        return min(round($percentage, 2), 100);
    }

    public function isAchieved(): bool
    {
        return $this->current_value >= $this->target_value;
    }
}
