<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPwaSetting extends Model
{
    use HasFactory;

    protected $table = 'tenant_pwa_settings';

    protected $fillable = [
        'tenant_id',
        'app_name',
        'short_name',
        'description',
        'icon_72',
        'icon_96',
        'icon_128',
        'icon_144',
        'icon_152',
        'icon_192',
        'icon_384',
        'icon_512',
        'splash_screen_path',
        'theme_color',
        'background_color',
        'display_mode',
        'orientation',
        'start_url',
        'scope',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
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
