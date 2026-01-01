<?php

namespace App\Core\Features;

use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureUsageLog extends Model
{
    // No updated_at needed as per schema? Schema has created_at index.
    // Usually logs are append-only.
    // Migration: $table->timestamp('created_at')->useCurrent();
    // Default timestamps are created_at and updated_at.
    // If we only want created_at, we should disable timestamps and manage created_at manually or via boot.
    // But standard model expects timestamps.
    // The user schema said "created_at. Index...".
    // I'll set public $timestamps = false; and protected $dates = ['created_at'];

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'feature_slug',
        'user_id',
        'action',
        'quantity',
        'result',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'metadata' => 'json',
        'created_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_slug', 'slug');
    }
}
