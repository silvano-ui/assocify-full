<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'domain',
        'tld',
        'status',
        'registrar_provider',
        'registrar_order_id',
        'registration_years',
        'price_paid',
        'currency',
        'contact_info',
        'dns_zone_id',
        'auto_renew',
        'registered_at',
        'expires_at',
        'transfer_code',
        'transfer_status',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'registration_years' => 'integer',
        'price_paid' => 'decimal:2',
        'contact_info' => 'array',
        'auto_renew' => 'boolean',
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_by' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                if (!$model->tenant_id && auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
