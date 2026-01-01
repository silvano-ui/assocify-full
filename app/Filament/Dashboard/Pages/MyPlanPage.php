<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use App\Core\Features\TenantFeature;
use App\Core\Tenant\Tenant;
use Illuminate\Support\Collection;

class MyPlanPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'My Plan';
    protected static ?string $title = 'My Plan & Features';
    protected static ?string $slug = 'my-plan';
    protected string $view = 'filament.dashboard.pages.my-plan-page';

    public ?Tenant $tenant = null;
    public Collection $features;

    public function mount(): void
    {
        $this->tenant = Filament::getTenant();

        if (!$this->tenant && auth()->check()) {
            $this->tenant = auth()->user()->tenant;
        }

        if ($this->tenant) {
            $this->features = TenantFeature::where('tenant_id', $this->tenant->id)
                ->with('feature')
                ->where('enabled', true)
                ->get();
        } else {
            $this->features = collect();
        }
    }

    public function getPlanNameProperty(): string
    {
        return $this->tenant?->plan?->name ?? 'No Plan';
    }

    public function isTrial(): bool
    {
        return $this->tenant && $this->tenant->trial_ends_at && $this->tenant->trial_ends_at->isFuture();
    }
    
    public function getTrialDaysLeftProperty(): int
    {
        return $this->isTrial() ? now()->diffInDays($this->tenant->trial_ends_at) : 0;
    }
}
