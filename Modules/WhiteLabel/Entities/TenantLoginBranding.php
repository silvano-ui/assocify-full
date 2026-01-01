<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantLoginBranding extends Model
{
    use HasFactory;

    protected $table = 'tenant_login_branding';

    protected $fillable = [
        'tenant_id',
        'page_title',
        'welcome_text',
        'welcome_subtext',
        'background_image_path',
        'background_color',
        'background_gradient',
        'show_logo',
        'logo_size',
        'show_social_login',
        'custom_css',
        'registration_enabled',
        'registration_welcome_text',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'show_logo' => 'boolean',
        'show_social_login' => 'boolean',
        'registration_enabled' => 'boolean',
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
