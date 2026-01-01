<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'key',
        'secret_hash',
        'type',
        'permissions',
        'rate_limit_per_minute',
        'rate_limit_per_day',
        'allowed_ips',
        'allowed_domains',
        'is_active',
        'last_used_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'allowed_ips' => 'array',
        'allowed_domains' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'rate_limit_per_minute' => 'integer',
        'rate_limit_per_day' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                // Set tenant_id - use user's tenant or default to 1 for SuperAdmin
                if (!$model->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id ?? 1;
                }
                
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
            
            if (!$model->key) {
                $model->key = 'pk_' . Str::random(32);
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
            // SuperAdmin (no tenant_id) sees all records - no filter applied
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(ApiKeyScope::class);
    }

    public function rateLimits(): HasMany
    {
        return $this->hasMany(ApiRateLimit::class);
    }
}
