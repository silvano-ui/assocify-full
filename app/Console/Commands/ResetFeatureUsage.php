<?php

namespace App\Console\Commands;

use App\Core\Features\TenantFeature;
use App\Facades\Features;
use Illuminate\Console\Command;

class ResetFeatureUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:reset-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset usage for features with periodic reset';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting feature usage...');

        // Logic to find features that need reset.
        // We need to look at PlanFeature reset_period for the tenant features.
        // Or TenantFeature reset_at.
        
        // Strategy: Iterate over TenantFeatures where reset_at <= now() or based on Plan configuration.
        // Since TenantFeature doesn't have "reset_period" column, we must look up the PlanFeature or store it.
        // Or "reset_at" on TenantFeature is the next reset date.
        
        $featuresToReset = TenantFeature::whereNotNull('reset_at')
            ->where('reset_at', '<=', now())
            ->get();

        foreach ($featuresToReset as $tenantFeature) {
            Features::resetUsage($tenantFeature->feature_slug, $tenantFeature->tenant_id);
            
            // Calculate next reset date
            // We need to know the period (monthly, daily, etc.)
            // We can look at PlanFeature or a stored period.
            // For now, let's assume monthly if not specified or lookup.
            
            // Lookup PlanFeature
            $tenant = $tenantFeature->tenant;
            if ($tenant && $tenant->plan_id) {
                 $planFeature = \App\Core\Features\PlanFeature::where('plan_id', $tenant->plan_id)
                    ->where('feature_slug', $tenantFeature->feature_slug)
                    ->first();
                 
                 if ($planFeature && $planFeature->reset_period) {
                     $nextReset = match($planFeature->reset_period) {
                         'daily' => now()->addDay(),
                         'weekly' => now()->addWeek(),
                         'monthly' => now()->addMonth(),
                         'yearly' => now()->addYear(),
                         default => null,
                     };
                     
                     if ($nextReset) {
                         $tenantFeature->update(['reset_at' => $nextReset]);
                     }
                 }
            }
        }

        $this->info('Feature usage reset complete.');
    }
}
