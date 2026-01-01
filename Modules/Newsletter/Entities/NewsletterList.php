<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Filament\Facades\Filament;

class NewsletterList extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'type',
        'dynamic_filters',
        'is_default',
        'is_active',
        'subscribers_count',
        'created_by',
    ];

    protected $casts = [
        'dynamic_filters' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'subscribers_count' => 'integer',
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

            // Auto-generate unique slug from name
            if (!$model->slug && $model->name) {
                $baseSlug = \Illuminate\Support\Str::slug($model->name);
                $slug = $baseSlug . '-' . strtolower(\Illuminate\Support\Str::random(8));

                // Ensure uniqueness within tenant
                while (static::withoutGlobalScopes()->where('tenant_id', $model->tenant_id)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . strtolower(\Illuminate\Support\Str::random(8));
                }

                $model->slug = $slug;
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

    public function subscribers(): HasMany
    {
        return $this->hasMany(NewsletterSubscriber::class, 'list_id');
    }
}
