<?php

namespace Modules\Documents\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Members\Entities\MemberCategory;

class DocumentAccess extends Model
{
    protected $table = 'document_access';

    protected $guarded = [];

    protected $casts = [
        'can_view' => 'boolean',
        'can_download' => 'boolean',
        'can_sign' => 'boolean',
    ];
    
    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check() && !$model->granted_by) {
                $model->granted_by = auth()->id();
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function memberCategory(): BelongsTo
    {
        return $this->belongsTo(MemberCategory::class);
    }

    public function granter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
