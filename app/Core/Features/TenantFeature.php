<?php

namespace App\Core\Features;

use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantFeature extends Model
{
    protected $fillable = [
        'tenant_id',
        'feature_slug',
        'source',
        'enabled',
        'limit_value',
        'used_value',
        'reset_at',
        'price_override',
        'is_trial',
        'trial_ends_at',
        'granted_by',
        'granted_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'limit_value' => 'integer',
        'used_value' => 'integer',
        'reset_at' => 'datetime',
        'price_override' => 'decimal:2',
        'is_trial' => 'boolean',
        'trial_ends_at' => 'datetime',
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
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

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_slug', 'slug');
    }

    public function granter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
