<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ApiEndpointConfig extends Model
{
    protected $fillable = [
        'tenant_id',
        'endpoint_pattern',
        'method',
        'is_enabled',
        'rate_limit_override',
        'cache_ttl_seconds',
        'require_auth',
        'allowed_scopes',
        'custom_headers',
        'transform_request',
        'transform_response',
        'created_by',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'rate_limit_override' => 'integer',
        'cache_ttl_seconds' => 'integer',
        'require_auth' => 'boolean',
        'allowed_scopes' => 'array',
        'custom_headers' => 'array',
        'transform_request' => 'array',
        'transform_response' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
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
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
