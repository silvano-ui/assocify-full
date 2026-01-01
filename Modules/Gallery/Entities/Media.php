<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'album_id',
        'user_id',
        'type',
        'disk',
        'path',
        'filename',
        'original_name',
        'size',
        'mime',
        'width',
        'height',
        'duration',
        'bitrate',
        'fps',
        'codec',
        'thumbnail_path',
        'poster_path',
        'webp_path',
        'gif_preview_path',
        'watermarked_path',
        'metadata',
        'exif_data',
        'caption',
        'is_featured',
        'views_count',
        'downloads_count',
        'likes_count',
        'comments_count',
        'sort_order',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'integer',
        'bitrate' => 'integer',
        'fps' => 'float',
        'metadata' => 'json',
        'exif_data' => 'json',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                $model->user_id = auth()->id();
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(MediaTag::class, 'media_tag_pivot', 'media_id', 'tag_id');
    }

    public function people(): HasMany
    {
        return $this->hasMany(MediaPeopleTag::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(MediaLocation::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_likes', 'media_id', 'user_id')
            ->withPivot('created_at');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MediaComment::class);
    }
}
