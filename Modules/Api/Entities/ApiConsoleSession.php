<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;
use Illuminate\Support\Str;

class ApiConsoleSession extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'api_key_id',
        'session_token',
        'name',
        'saved_requests',
        'environment_vars',
        'expires_at',
        'last_activity_at',
    ];

    protected $casts = [
        'saved_requests' => 'array',
        'environment_vars' => 'array',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
            }
            if (!$model->session_token) {
                $model->session_token = Str::random(64);
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ApiConsoleHistory::class, 'session_id');
    }
}
