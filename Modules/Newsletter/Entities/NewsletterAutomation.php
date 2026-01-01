<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Filament\Facades\Filament;

class NewsletterAutomation extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'trigger_type',
        'trigger_config',
        'is_active',
        'total_sent',
        'total_opened',
        'total_clicked',
        'created_by',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'is_active' => 'boolean',
        'total_sent' => 'integer',
        'total_opened' => 'integer',
        'total_clicked' => 'integer',
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

    public function steps(): HasMany
    {
        return $this->hasMany(NewsletterAutomationStep::class, 'automation_id');
    }
}
