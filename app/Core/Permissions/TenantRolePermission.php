<?php

namespace App\Core\Permissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantRolePermission extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'tenant_role_id',
        'permission_slug',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(TenantRole::class, 'tenant_role_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_slug', 'slug');
    }
}
