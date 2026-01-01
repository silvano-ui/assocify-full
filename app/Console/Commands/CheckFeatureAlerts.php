<?php

namespace App\Console\Commands;

use App\Core\Features\TenantFeature;
use App\Facades\Features;
use Illuminate\Console\Command;

class CheckFeatureAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring trials and limits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking feature alerts...');

        // 1. Check expiring trials
        // We iterate all tenants? Or query all expiring features.
        // Better query all expiring features directly.
        
        $expiringTrials = TenantFeature::where('is_trial', true)
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->where('trial_ends_at', '>', now())
            //->whereDoesntHave('alerts', ...) // Prevent duplicate alerts
            ->get();

        foreach ($expiringTrials as $tf) {
            // TODO: Send notification to tenant owner
            $this->info("Trial expiring for Tenant {$tf->tenant_id}, Feature {$tf->feature_slug}");
            // Features::sendTrialExpiringAlert($tf);
        }

        // 2. Check limits (already checked on increment, but maybe we want a daily digest?)
        // Skipping for now as increment handles real-time alerts.

        $this->info('Alert check complete.');
    }
}
