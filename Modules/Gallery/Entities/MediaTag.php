<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaTag extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'color',
        'media_count',
    ];

    protected $casts = [
        'media_count' => 'integer',
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

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_tag_pivot', 'tag_id', 'media_id');
    }
}
