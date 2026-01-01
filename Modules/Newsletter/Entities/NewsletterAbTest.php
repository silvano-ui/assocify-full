<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterAbTest extends Model
{
    protected $fillable = [
        'campaign_id',
        'test_type',
        'variant_a',
        'variant_b',
        'winner_criteria',
        'test_size_percent',
        'test_duration_hours',
        'status',
        'winner',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'variant_a' => 'array',
        'variant_b' => 'array',
        'test_size_percent' => 'integer',
        'test_duration_hours' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class);
    }
}
