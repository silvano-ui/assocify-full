<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiAlertNotification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'alert_id',
        'channel',
        'recipient',
        'message',
        'status',
        'sent_at',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function alert(): BelongsTo
    {
        return $this->belongsTo(ApiAlert::class);
    }
}
