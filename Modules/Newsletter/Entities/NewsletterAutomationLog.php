<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class NewsletterAutomationLog extends Model
{
    protected $fillable = [
        'automation_id',
        'step_id',
        'subscriber_id',
        'user_id',
        'email',
        'status',
        'error_message',
        'scheduled_at',
        'executed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
    ];

    public function automation(): BelongsTo
    {
        return $this->belongsTo(NewsletterAutomation::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(NewsletterAutomationStep::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscriber::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
