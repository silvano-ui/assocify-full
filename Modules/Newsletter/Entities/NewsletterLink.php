<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterLink extends Model
{
    protected $fillable = [
        'campaign_id',
        'original_url',
        'tracking_url',
        'click_count',
        'unique_clicks',
        'position',
    ];

    protected $casts = [
        'click_count' => 'integer',
        'unique_clicks' => 'integer',
        'position' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class);
    }
}
