<?php

namespace Modules\WhiteLabel\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainDnsRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'type',
        'name',
        'value',
        'ttl',
        'priority',
        'is_system',
        'is_verified',
        'provider_record_id',
    ];

    protected $casts = [
        'domain_id' => 'integer',
        'ttl' => 'integer',
        'priority' => 'integer',
        'is_system' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(TenantDomain::class, 'domain_id');
    }
}
