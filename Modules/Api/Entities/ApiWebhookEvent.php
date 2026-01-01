<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiWebhookEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'webhook_id',
        'event_type',
        'payload',
        'status',
        'attempts',
        'last_attempt_at',
        'next_retry_at',
        'response_status',
        'response_body',
        'error_message',
        'sent_at',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'response_status' => 'integer',
        'last_attempt_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(ApiWebhook::class, 'webhook_id');
    }
}
