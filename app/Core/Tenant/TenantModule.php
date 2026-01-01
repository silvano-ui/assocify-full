<?php

namespace App\Core\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'settings' => 'array',
        'enabled_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
