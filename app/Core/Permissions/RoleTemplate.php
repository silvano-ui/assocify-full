<?php

namespace App\Core\Permissions;

use Illuminate\Database\Eloquent\Model;

class RoleTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'permissions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];
}
