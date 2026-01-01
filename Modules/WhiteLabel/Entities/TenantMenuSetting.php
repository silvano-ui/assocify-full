<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMenuSetting extends Model
{
    use HasFactory;

    protected $table = 'tenant_menu_settings';

    protected $fillable = [
        'tenant_id',
        'hidden_modules',
        'custom_menu_items',
        'menu_order',
        'quick_actions',
        'dashboard_widgets',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'hidden_modules' => 'array',
        'custom_menu_items' => 'array',
        'menu_order' => 'array',
        'quick_actions' => 'array',
        'dashboard_widgets' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
