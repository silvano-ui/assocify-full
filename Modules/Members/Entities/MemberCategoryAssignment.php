<?php

namespace Modules\Members\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCategoryAssignment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_profile_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MemberCategory::class, 'member_category_id');
    }
}
