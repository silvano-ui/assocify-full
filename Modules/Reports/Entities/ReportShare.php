<?php

namespace Modules\Reports\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Illuminate\Support\Str;

class ReportShare extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
        'access_log' => 'json',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
            
            if (empty($model->token)) {
                $model->token = Str::random(64);
            }
            
            if (empty($model->expires_at)) {
                $model->expires_at = now()->addDays(7);
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function generatedReport(): BelongsTo
    {
        return $this->belongsTo(GeneratedReport::class);
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function recordAccess(): void
    {
        $this->last_accessed_at = now();
        $this->access_count = ($this->access_count ?? 0) + 1;
        $this->save();
    }

    public function getPublicUrl(): string
    {
        return route('reports.share.view', ['token' => $this->token]);
    }
}
