<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiConsoleHistory extends Model
{
    public $timestamps = false;
    protected $table = 'api_console_history';

    protected $fillable = [
        'session_id',
        'method',
        'endpoint',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
        'response_time_ms',
        'executed_at',
        'created_at',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_headers' => 'array',
        'response_body' => 'array',
        'response_status' => 'integer',
        'response_time_ms' => 'integer',
        'executed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ApiConsoleSession::class, 'session_id');
    }
}
