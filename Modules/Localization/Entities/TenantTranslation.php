<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\Tenant\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantTranslation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_auto_translated' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $query->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }

    // Relations
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
