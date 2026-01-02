<?php

namespace Modules\Reports\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Carbon\Carbon;

class ScheduledReport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'recipients' => 'json',
        'schedule_options' => 'json',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $query->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    public function generatedReports(): HasMany
    {
        return $this->hasMany(GeneratedReport::class);
    }

    public function scopeDue($query)
    {
        return $query->where('is_active', true)
                     ->where('next_run_at', '<=', now());
    }

    public function calculateNextRun(): void
    {
        // Simple implementation based on frequency
        // Assuming frequency is stored in a 'frequency' column (daily, weekly, monthly)
        $now = Carbon::now();
        
        if ($this->frequency === 'daily') {
            $this->next_run_at = $now->addDay();
        } elseif ($this->frequency === 'weekly') {
            $this->next_run_at = $now->addWeek();
        } elseif ($this->frequency === 'monthly') {
            $this->next_run_at = $now->addMonth();
        }
        
        $this->save();
    }

    public function markAsRun(): void
    {
        $this->last_run_at = now();
        $this->calculateNextRun();
    }

    public function markAsFailed($error): void
    {
        // Log error logic here or just update status
        // For now just updating last run to avoid immediate retry loop if not desired
        // Or keep next_run_at same to retry?
        // Usually better to mark last run and schedule next to avoid infinite loop
        $this->last_run_at = now();
        // Maybe store error in a log or field
    }
}
