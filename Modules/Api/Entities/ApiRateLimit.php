<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRateLimit extends Model
{
    protected $fillable = [
        'api_key_id',
        'period',
        'limit_value',
        'current_count',
        'reset_at',
    ];

    protected $casts = [
        'limit_value' => 'integer',
        'current_count' => 'integer',
        'reset_at' => 'datetime',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
