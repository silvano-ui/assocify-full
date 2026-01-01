<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ApiIpWhitelist extends Model
{
    protected $table = 'api_ip_whitelist';
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'ip_address',
        'cidr_range',
        'label',
        'is_active',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
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
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
            // SuperAdmin (no tenant_id) sees all records - no filter applied
        });
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
