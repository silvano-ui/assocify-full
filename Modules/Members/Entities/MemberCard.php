<?php

namespace Modules\Members\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use chillerlan\QRCode\QRCode;

class MemberCard extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($card) {
            if (empty($card->card_number)) {
                // Generate a random card number if not provided
                $card->card_number = strtoupper('MEM-' . uniqid());
            }
            if (empty($card->qr_code)) {
                // Generate QR code content (SVG by default)
                $card->qr_code = (new QRCode)->render($card->card_number);
            }
        });
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_profile_id');
    }
}
