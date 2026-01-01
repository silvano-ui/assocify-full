<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoProcessingJob extends Model
{
    protected $fillable = [
        'media_id',
        'tenant_id',
        'job_type',
        'status',
        'progress',
        'error_message',
    ];

    protected $casts = [
        'progress' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
