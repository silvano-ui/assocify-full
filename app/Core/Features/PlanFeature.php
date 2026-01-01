<?php

namespace App\Core\Features;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    protected $fillable = [
        'plan_id',
        'feature_slug',
        'included',
        'limit_value',
        'limit_type',
        'reset_period',
        'soft_limit',
    ];

    protected $casts = [
        'included' => 'boolean',
        'limit_value' => 'integer',
        'soft_limit' => 'boolean',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_slug', 'slug');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(\App\Core\Plans\Plan::class);
    }
}
