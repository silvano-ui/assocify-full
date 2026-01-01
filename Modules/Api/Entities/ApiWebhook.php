<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;

class ApiWebhook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'url',
        'secret',
        'events',
        'headers',
        'is_active',
        'retry_count',
        'timeout_seconds',
        'last_triggered_at',
        'last_status',
        'failure_count',
        'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'retry_count' => 'integer',
        'timeout_seconds' => 'integer',
        'failure_count' => 'integer',
        'last_triggered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
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
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ApiWebhookEvent::class, 'webhook_id');
    }
}
