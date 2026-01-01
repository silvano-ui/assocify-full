<?php

namespace Modules\Payments\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Members\Entities\MemberProfile;
use Modules\Members\Entities\MemberCategory;

class SubscriptionRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'member_profile_id',
        'member_category_id',
        'invoice_id',
        'year',
        'amount',
        'status',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function memberProfile()
    {
        return $this->belongsTo(MemberProfile::class);
    }

    public function memberCategory()
    {
        return $this->belongsTo(MemberCategory::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }
}
