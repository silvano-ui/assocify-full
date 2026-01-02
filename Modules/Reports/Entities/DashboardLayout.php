<?php

namespace Modules\Reports\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;

class DashboardLayout extends Model
{
    protected $guarded = [];

    protected $casts = [
        'layout_config' => 'json',
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $query->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
