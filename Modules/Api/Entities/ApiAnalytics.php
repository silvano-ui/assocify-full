<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiAnalytics extends Model
{
    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'date',
        'hour',
        'endpoint',
        'method',
        'total_requests',
        'successful_requests',
        'failed_requests',
        'avg_response_time_ms',
        'min_response_time_ms',
        'max_response_time_ms',
        'total_bytes_in',
        'total_bytes_out',
        'unique_ips',
        'error_4xx_count',
        'error_5xx_count',
    ];

    protected $casts = [
        'date' => 'date',
        'hour' => 'integer',
        'total_requests' => 'integer',
        'successful_requests' => 'integer',
        'failed_requests' => 'integer',
        'avg_response_time_ms' => 'decimal:2',
        'min_response_time_ms' => 'integer',
        'max_response_time_ms' => 'integer',
        'total_bytes_in' => 'integer',
        'total_bytes_out' => 'integer',
        'unique_ips' => 'integer',
        'error_4xx_count' => 'integer',
        'error_5xx_count' => 'integer',
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
}
