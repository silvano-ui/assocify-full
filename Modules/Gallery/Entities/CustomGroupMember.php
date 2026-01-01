<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomGroupMember extends Pivot
{
    public $timestamps = false;
    
    protected $table = 'custom_group_members';

    protected $fillable = [
        'custom_group_id',
        'user_id',
        'added_by',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->added_by = auth()->id();
            }
        });
    }

    public function customGroup(): BelongsTo
    {
        return $this->belongsTo(CustomGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
