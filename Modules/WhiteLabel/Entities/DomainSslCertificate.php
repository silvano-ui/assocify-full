<?php

namespace Modules\WhiteLabel\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainSslCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'provider',
        'certificate',
        'private_key_encrypted',
        'ca_bundle',
        'issued_at',
        'expires_at',
        'auto_renew',
        'last_renewal_attempt',
        'renewal_error',
        'status',
    ];

    protected $casts = [
        'domain_id' => 'integer',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_renew' => 'boolean',
        'last_renewal_attempt' => 'datetime',
    ];

    protected $hidden = [
        'private_key_encrypted',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(TenantDomain::class, 'domain_id');
    }
}
