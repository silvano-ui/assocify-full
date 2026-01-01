<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class EventRegistration extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid' => 'boolean',
        'registered_at' => 'datetime',
        'approved_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
