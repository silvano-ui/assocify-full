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
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }

            // Auto-generate slug from name
            if (!$model->slug && $model->name) {
                $model->slug = \Illuminate\Support\Str::slug($model->name) . '-' . \Illuminate\Support\Str::random(8);
            }

            // Default list_ids to empty array
            if (!$model->list_ids) {
                $model->list_ids = [];
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
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
