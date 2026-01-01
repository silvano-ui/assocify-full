<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;

class WatermarkSetting extends Model
{
    protected $fillable = [
        'tenant_id',
        'enabled',
        'type',
        'text',
        'image_path',
        'position',
        'opacity',
        'size',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'opacity' => 'integer',
        'size' => 'integer',
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
}
