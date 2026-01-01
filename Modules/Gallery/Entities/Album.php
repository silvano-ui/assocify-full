<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\Users\User;
use Modules\Events\Entities\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'event_id',
        'name',
        'slug',
        'description',
        'cover_media_id',
        'visibility',
        'is_collaborative',
        'password',
        'download_enabled',
        'allow_comments',
        'allow_likes',
        'allow_external_share',
        'share_token',
        'share_expires_at',
        'qr_code_path',
        'sort_order',
        'views_count',
        'created_by',
        'settings',
    ];

    protected $casts = [
        'is_collaborative' => 'boolean',
        'download_enabled' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_likes' => 'boolean',
        'allow_external_share' => 'boolean',
        'share_expires_at' => 'datetime',
        'settings' => 'json',
        'views_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                if (auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
                $model->created_by = auth()->id();
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Album::class, 'parent_id');
    }

    public function access(): HasMany
    {
        return $this->hasMany(AlbumAccess::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(AlbumCollaborator::class);
    }

    public function shareLinks(): HasMany
    {
        return $this->hasMany(AlbumShareLink::class);
    }
}
