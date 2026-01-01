<?php

namespace Modules\Members\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;

class MemberProfile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
        'document_expires' => 'date',
        'custom_fields' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        // Global scope per filtrare per tenant
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(MemberCard::class);
    }

    public function categoryAssignments(): HasMany
    {
        return $this->hasMany(MemberCategoryAssignment::class);
    }
}
