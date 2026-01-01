<?php

namespace Modules\WhiteLabel\Entities;

use App\Core\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPdfBranding extends Model
{
    use HasFactory;

    protected $table = 'tenant_pdf_branding';

    protected $fillable = [
        'tenant_id',
        'header_logo_path',
        'header_text',
        'footer_text',
        'footer_logo_path',
        'watermark_text',
        'watermark_image_path',
        'watermark_opacity',
        'primary_color',
        'font_family',
        'paper_size',
        'margins',
        'invoice_template',
        'receipt_template',
        'certificate_template',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'watermark_opacity' => 'integer',
        'margins' => 'array',
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
