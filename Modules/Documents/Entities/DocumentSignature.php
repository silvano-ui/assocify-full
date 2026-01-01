<?php

namespace Modules\Documents\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignature extends Model
{
    protected $guarded = [];

    protected $casts = [
        'certificate_data' => 'array',
        'device_info' => 'array',
        'otp_verified_at' => 'datetime',
        'requested_at' => 'datetime',
        'signed_at' => 'datetime',
        'expires_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
