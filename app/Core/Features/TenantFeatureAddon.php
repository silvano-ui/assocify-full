<?php

namespace App\Core\Features;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantFeatureAddon extends Model
{
    protected $fillable = [
        'tenant_id',
        'feature_slug',
        'bundle_id',
        'quantity',
        'price_paid',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'auto_renew',
        'cancelled_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_paid' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'cancelled_at' => 'datetime',
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

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(FeatureBundle::class, 'bundle_id');
    }
}
