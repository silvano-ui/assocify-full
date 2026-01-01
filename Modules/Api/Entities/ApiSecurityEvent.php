<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ApiSecurityEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'event_type',
        'ip_address',
        'user_agent',
        'endpoint',
        'details',
        'severity',
        'acknowledged_at',
        'acknowledged_by',
        'created_at',
    ];

    protected $casts = [
        'details' => 'array',
        'acknowledged_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                // Set tenant_id - use user's tenant or default to 1 for SuperAdmin
                if (!$model->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id ?? 1;
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

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function acknowledger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}
