<?php

namespace App\Core\Features;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'module',
        'category',
        'is_premium',
        'is_beta',
        'price_monthly',
        'price_yearly',
        'price_per_unit',
        'unit_name',
        'is_active',
        'sort_order',
        'icon',
        'settings',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_beta' => 'boolean',
        'is_active' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'settings' => 'json',
        'sort_order' => 'integer',
    ];

    public function dependencies(): HasMany
    {
        return $this->hasMany(FeatureDependency::class, 'feature_slug', 'slug');
    }

    public function requiredBy(): HasMany
    {
        return $this->hasMany(FeatureDependency::class, 'requires_feature_slug', 'slug');
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(FeatureBundle::class, 'feature_bundle_items', 'feature_slug', 'feature_bundle_id', 'slug', 'id');
    }

    public function planFeatures(): HasMany
    {
        return $this->hasMany(PlanFeature::class, 'feature_slug', 'slug');
    }

    public function tenantFeatures(): HasMany
    {
        return $this->hasMany(TenantFeature::class, 'feature_slug', 'slug');
    }
}
