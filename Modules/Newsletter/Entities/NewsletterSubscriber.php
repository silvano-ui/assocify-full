<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Filament\Facades\Filament;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'tenant_id',
        'list_id',
        'user_id',
        'email',
        'name',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'bounce_type',
        'consent_given',
        'consent_at',
        'consent_ip',
        'source',
        'metadata',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'consent_given' => 'boolean',
        'consent_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
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

    public function list(): BelongsTo
    {
        return $this->belongsTo(NewsletterList::class, 'list_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
