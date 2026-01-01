<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'album_id',
        'media_ids',
        'zip_path',
        'status',
        'file_size',
        'download_token',
        'expires_at',
    ];

    protected $casts = [
        'media_ids' => 'json',
        'file_size' => 'integer',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
            if (empty($model->download_token)) {
                $model->download_token = \Illuminate\Support\Str::random(64);
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
