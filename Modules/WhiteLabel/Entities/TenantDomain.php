<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDomain extends Model
{
    use HasFactory;

    protected $table = 'tenant_domains';

    protected $fillable = [
        'tenant_id',
        'domain',
        'type',
        'is_primary',
        'is_verified',
        'verification_token',
        'verified_at',
        'ssl_status',
        'ssl_expires_at',
        'dns_records',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'ssl_expires_at' => 'datetime',
        'dns_records' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
