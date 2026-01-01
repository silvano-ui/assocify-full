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
            if (!$model->tenant_id && Filament::getTenant()) {
                $model->tenant_id = Filament::getTenant()->id;
            }
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
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
