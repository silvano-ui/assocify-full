<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Users\User;

class MediaComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'media_id',
        'user_id',
        'parent_id',
        'body',
        'is_pinned',
        'likes_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'likes_count' => 'integer',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MediaComment::class, 'parent_id');
    }
}
