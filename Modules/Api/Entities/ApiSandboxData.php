<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiSandboxData extends Model
{
    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'resource_type',
        'resource_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id && !$model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
