<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'registration_starts' => 'datetime',
        'registration_ends' => 'datetime',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'requires_approval' => 'boolean',
        'is_public' => 'boolean',
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }
}
