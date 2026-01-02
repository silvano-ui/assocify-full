<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Translation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_auto_translated' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    // Relations
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }

    // Scopes
    public function scopeForLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    public function scopeForGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeUnreviewed($query)
    {
        return $query->whereNull('reviewed_at');
    }

    public function scopeAutoTranslated($query)
    {
        return $query->where('is_auto_translated', true);
    }
}
