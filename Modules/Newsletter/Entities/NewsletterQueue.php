<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Tenant\Tenant;
use Filament\Facades\Filament;

class NewsletterQueue extends Model
{
    protected $table = 'newsletter_queue';

    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'automation_id',
        'subscriber_id',
        'email',
        'subject',
        'html_content',
        'text_content',
        'from_name',
        'from_email',
        'priority',
        'attempts',
        'max_attempts',
        'status',
        'error_message',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'priority' => 'integer',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && Filament::getTenant()) {
                $model->tenant_id = Filament::getTenant()->id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class);
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(NewsletterAutomation::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscriber::class);
    }
}
