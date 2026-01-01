<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ApiDocumentation extends Model
{
    protected $table = 'api_documentation';

    protected $fillable = [
        'tenant_id',
        'version',
        'title',
        'description',
        'openapi_spec',
        'is_public',
        'is_current',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'openapi_spec' => 'array',
        'is_public' => 'boolean',
        'is_current' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id && !$model->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where(function($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id)
                      ->orWhereNull('tenant_id'); // Allow viewing public docs if tenant_id is null? Or strict tenant isolation?
                      // Usually documentation might be global or tenant specific. 
                      // For now strict tenant isolation if tenant_id is present in DB.
                      // But the prompt said tenant_id nullable.
                });
                // If tenant_id is nullable, it implies system-wide docs or tenant-specific.
                // Let's assume strict filtering if the record has tenant_id.
                // But global scope usually filters by tenant_id = current.
                
                // Let's stick to strict tenant check for now if the user belongs to a tenant.
                // But wait, if I am a tenant user, I should see my tenant's docs AND maybe global docs?
                // The prompt "ENTITIES con booted() per tenant_id" usually implies strict multi-tenancy.
                // However, api_documentation has tenant_id nullable.
                
                // I will modify the scope to be: where tenant_id = current OR tenant_id IS NULL (if we want global docs visible).
                // But safer to just filter by tenant_id if set on user, and if the record is tenant specific.
                
                // Let's implement standard tenant scope:
                $query->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id)
                      ->orWhereNull('tenant_id');
                });
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
