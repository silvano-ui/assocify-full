<?php

namespace App\Core\Permissions;

use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TenantRole extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'slug',
        'name',
        'description',
        'color',
        'icon',
        'is_default',
        'is_system',
        'can_be_deleted',
        'hierarchy_level',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'can_be_deleted' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $builder->where('tenant_roles.tenant_id', Auth::user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(TenantRolePermission::class);
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }
}
