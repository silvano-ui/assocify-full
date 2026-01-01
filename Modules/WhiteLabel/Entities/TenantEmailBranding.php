<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantEmailBranding extends Model
{
    use HasFactory;

    protected $table = 'tenant_email_branding';

    protected $fillable = [
        'tenant_id',
        'from_name',
        'from_email',
        'reply_to',
        'email_header_html',
        'email_footer_html',
        'email_signature',
        'email_logo_path',
        'email_primary_color',
        'email_template_overrides',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'email_template_overrides' => 'array',
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
