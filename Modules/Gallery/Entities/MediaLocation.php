<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLocation extends Model
{
    protected $fillable = [
        'media_id',
        'latitude',
        'longitude',
        'altitude',
        'address',
        'city',
        'country',
        'gpx_track_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude' => 'integer',
        'gpx_track_id' => 'integer',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
