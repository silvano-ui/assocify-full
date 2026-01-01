<?php

namespace App\Core\Features;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureDependency extends Model
{
    protected $fillable = [
        'feature_slug',
        'requires_feature_slug',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_slug', 'slug');
    }

    public function requiredFeature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'requires_feature_slug', 'slug');
    }
}
