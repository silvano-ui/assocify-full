<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Tenant;

class TranslationSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'api_key' => 'encrypted',
        'monthly_char_limit' => 'integer',
        'chars_used_this_month' => 'integer',
        'reset_day' => 'integer',
    ];

    // Relations
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Scopes
    public function scopeForTenant($query, $tenant_id)
    {
        return $query->where('tenant_id', $tenant_id);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function hasQuotaRemaining(int $chars = 0): bool
    {
        if (is_null($this->monthly_char_limit)) {
            return true; // No limit
        }

        return ($this->chars_used_this_month + $chars) <= $this->monthly_char_limit;
    }

    public function incrementUsage(int $chars): void
    {
        $this->chars_used_this_month += $chars;
        $this->save();
    }

    public function resetMonthlyUsage(): void
    {
        $this->chars_used_this_month = 0;
        $this->save();
    }
}
