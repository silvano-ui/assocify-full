<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantBranding extends Model
{
    use HasFactory;

    protected $table = 'tenant_branding';

    protected $fillable = [
        'tenant_id',
        'logo_path',
        'logo_dark_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'success_color',
        'warning_color',
        'danger_color',
        'background_color',
        'sidebar_color',
        'text_color',
        'font_family',
        'font_url',
        'heading_font',
        'custom_css',
        'custom_js',
        'theme_mode',
        'preset_theme',
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
