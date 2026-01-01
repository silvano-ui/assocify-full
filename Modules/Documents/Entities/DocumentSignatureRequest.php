<?php

namespace Modules\Documents\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignatureRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_reminder_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function signerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_user_id');
    }
}
