<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;
use Illuminate\Support\Str;

class ApiOauthClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'client_id',
        'client_secret_hash',
        'redirect_uris',
        'grant_types',
        'scopes',
        'is_confidential',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'redirect_uris' => 'array',
        'grant_types' => 'array',
        'scopes' => 'array',
        'is_confidential' => 'boolean',
        'is_active' => 'boolean',
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
            
            // Auto-generate client_id if not set
            if (!$model->client_id) {
                $model->client_id = \Illuminate\Support\Str::uuid()->toString();
            }
            
            // Auto-generate client_secret_hash if not set
            if (!$model->client_secret_hash) {
                $model->client_secret_hash = hash('sha256', \Illuminate\Support\Str::random(64));
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
            // SuperAdmin (no tenant_id) sees all records - no filter applied
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(ApiOauthToken::class, 'oauth_client_id');
    }
}
