<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Filament\Facades\Filament;

class NewsletterTemplate extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'type',
        'category',
        'thumbnail_path',
        'html_content',
        'mjml_content',
        'json_design',
        'variables',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'json_design' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
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

            // Auto-generate slug from name
            if (!$model->slug && $model->name) {
                $model->slug = \Illuminate\Support\Str::slug($model->name) . '-' . \Illuminate\Support\Str::random(8);
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
