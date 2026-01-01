<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;

class ApiAlert extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'conditions',
        'notification_channels',
        'is_active',
        'last_triggered_at',
        'trigger_count',
        'cooldown_minutes',
        'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'notification_channels' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'trigger_count' => 'integer',
        'cooldown_minutes' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                // Set tenant_id - use user's tenant or default to 1 for SuperAdmin
                if (!$model->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id ?? 1;
                }
                
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
            // SuperAdmin (no tenant_id) sees all records - no filter applied
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ApiAlertNotification::class, 'alert_id');
    }
}
