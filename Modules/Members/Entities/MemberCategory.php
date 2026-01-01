<?php

namespace Modules\Members\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Tenant\Tenant;

class MemberCategory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'annual_fee' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(MemberCategoryAssignment::class);
    }
}
