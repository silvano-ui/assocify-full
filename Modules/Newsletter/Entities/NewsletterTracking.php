<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterTracking extends Model
{
    protected $table = 'newsletter_tracking';

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'email',
        'event_type',
        'link_url',
        'link_index',
        'ip_address',
        'user_agent',
        'location',
        'device_type',
        'email_client',
        'opened_at',
        'clicked_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'link_index' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscriber::class);
    }
}
