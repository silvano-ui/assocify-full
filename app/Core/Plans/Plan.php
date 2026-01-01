<?php

namespace App\Core\Plans;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'modules' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];
}
