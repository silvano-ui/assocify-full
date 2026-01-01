<?php

namespace App\Core\Permissions;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'module',
        'category',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
