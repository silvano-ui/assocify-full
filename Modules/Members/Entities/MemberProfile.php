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
