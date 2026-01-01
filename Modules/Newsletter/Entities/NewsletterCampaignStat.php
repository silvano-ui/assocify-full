<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterCampaignStat extends Model
{
    protected $fillable = [
        'campaign_id',
        'sent',
        'delivered',
        'opened',
        'unique_opens',
        'clicked',
        'unique_clicks',
        'bounced',
        'soft_bounced',
        'hard_bounced',
        'complained',
        'unsubscribed',
        'open_rate',
        'click_rate',
        'bounce_rate',
        'last_updated_at',
    ];

    protected $casts = [
        'sent' => 'integer',
        'delivered' => 'integer',
        'opened' => 'integer',
        'unique_opens' => 'integer',
        'clicked' => 'integer',
        'unique_clicks' => 'integer',
        'bounced' => 'integer',
        'soft_bounced' => 'integer',
        'hard_bounced' => 'integer',
        'complained' => 'integer',
        'unsubscribed' => 'integer',
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
        'last_updated_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class);
    }
}
