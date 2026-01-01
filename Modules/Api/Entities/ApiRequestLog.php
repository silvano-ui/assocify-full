<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ApiRequestLog extends Model
{
    public $timestamps = false;
    protected $table = 'api_requests_log';

    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'user_id',
        'method',
        'endpoint',
        'ip_address',
        'user_agent',
        'request_headers',
        'request_body',
        'response_status',
        'response_time_ms',
        'response_size_bytes',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'created_at' => 'datetime',
        'response_status' => 'integer',
        'response_time_ms' => 'integer',
        'response_size_bytes' => 'integer',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
