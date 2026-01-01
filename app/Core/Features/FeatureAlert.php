<?php

namespace App\Core\Features;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureAlert extends Model
{
    protected $fillable = [
        'tenant_id',
        'feature_slug',
        'alert_type',
        'threshold_percent',
        'sent_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'threshold_percent' => 'integer',
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_slug', 'slug');
    }
}
