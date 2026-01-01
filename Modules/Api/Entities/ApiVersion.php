<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;

class ApiVersion extends Model
{
    protected $fillable = [
        'tenant_id',
        'version',
        'status',
        'description',
        'changelog',
        'breaking_changes',
        'deprecation_date',
        'sunset_date',
        'migration_guide',
    ];

    protected $casts = [
        'breaking_changes' => 'array',
        'deprecation_date' => 'date',
        'sunset_date' => 'date',
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
