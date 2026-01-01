<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;

class ApiSdk extends Model
{
    protected $fillable = [
        'tenant_id',
        'language',
        'version',
        'download_url',
        'package_name',
        'repository_url',
        'documentation_url',
        'is_official',
        'is_active',
        'downloads_count',
        'generated_at',
    ];

    protected $casts = [
        'is_official' => 'boolean',
        'is_active' => 'boolean',
        'downloads_count' => 'integer',
        'generated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id && !$model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where(function($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id)
                      ->orWhereNull('tenant_id');
                });
            }
        });
    }
}
