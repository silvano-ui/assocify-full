<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Modules\Events\Entities\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumAccess extends Model
{
    protected $table = 'album_access';

    protected $fillable = [
        'album_id',
        'access_type',
        'user_id',
        'event_id',
        'member_category_id',
        'role_name',
        'custom_group_id',
        'can_view',
        'can_upload',
        'can_download',
        'can_share',
        'granted_by',
        'granted_at',
        'expires_at',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_upload' => 'boolean',
        'can_download' => 'boolean',
        'can_share' => 'boolean',
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->granted_by = auth()->id();
            }
        });
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Assuming MemberCategory model exists
    public function memberCategory(): BelongsTo
    {
        // Adjust namespace if needed
        return $this->belongsTo(\Modules\Members\Entities\MemberCategory::class);
    }

    public function customGroup(): BelongsTo
    {
        return $this->belongsTo(CustomGroup::class);
    }

    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
