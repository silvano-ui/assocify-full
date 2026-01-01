<?php

namespace Modules\Documents\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'requires_signature' => 'boolean',
        'requires_acknowledgment' => 'boolean',
        'is_template' => 'boolean',
        'is_locked' => 'boolean',
        'expires_at' => 'datetime',
        'locked_at' => 'datetime',
        'published_at' => 'datetime',
        'template_fields' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (!$model->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                if (!$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
        });

        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function access(): HasMany
    {
        return $this->hasMany(DocumentAccess::class);
    }
    
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
