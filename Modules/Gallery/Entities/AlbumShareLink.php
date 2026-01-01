<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumShareLink extends Model
{
    protected $fillable = [
        'album_id',
        'created_by',
        'token',
        'name',
        'password',
        'allow_download',
        'max_views',
        'views_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'allow_download' => 'boolean',
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'max_views' => 'integer',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
