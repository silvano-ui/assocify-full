<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterAutomationStep extends Model
{
    protected $fillable = [
        'automation_id',
        'step_order',
        'action_type',
        'template_id',
        'email_subject',
        'wait_duration',
        'wait_unit',
        'condition_type',
        'condition_config',
        'is_active',
        'sent_count',
    ];

    protected $casts = [
        'step_order' => 'integer',
        'condition_config' => 'array',
        'is_active' => 'boolean',
        'sent_count' => 'integer',
        'wait_duration' => 'integer',
    ];

    public function automation(): BelongsTo
    {
        return $this->belongsTo(NewsletterAutomation::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NewsletterTemplate::class);
    }
}
