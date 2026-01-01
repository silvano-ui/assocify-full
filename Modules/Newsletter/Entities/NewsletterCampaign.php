<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Filament\Facades\Filament;

class NewsletterCampaign extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'subject',
        'preview_text',
        'from_name',
        'from_email',
        'reply_to',
        'template_id',
        'html_content',
        'text_content',
        'list_ids',
        'segment_filters',
        'status',
        'send_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'created_by',
    ];

    protected $casts = [
        'list_ids' => 'array',
        'segment_filters' => 'array',
        'send_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && Filament::getTenant()) {
                $model->tenant_id = Filament::getTenant()->id;
            }
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NewsletterTemplate::class);
    }
}
