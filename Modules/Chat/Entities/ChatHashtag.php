<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatHashtag extends Model
{
    protected $fillable = [
        'tenant_id',
        'tag',
        'message_count',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'message_count' => 'integer',
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

    public function messages(): BelongsToMany
    {
        return $this->belongsToMany(Message::class, 'message_hashtags', 'hashtag_id', 'message_id');
    }
}
