<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slideshow extends Model
{
    protected $fillable = [
        'tenant_id',
        'album_id',
        'collection_id',
        'name',
        'duration_per_slide',
        'transition_type',
        'auto_play',
        'loop',
        'show_captions',
        'background_music_path',
        'created_by',
    ];

    protected $casts = [
        'duration_per_slide' => 'integer',
        'auto_play' => 'boolean',
        'loop' => 'boolean',
        'show_captions' => 'boolean',
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

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
