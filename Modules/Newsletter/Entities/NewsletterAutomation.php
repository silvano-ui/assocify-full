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

    public function steps(): HasMany
    {
        return $this->hasMany(NewsletterAutomationStep::class, 'automation_id');
    }
}
