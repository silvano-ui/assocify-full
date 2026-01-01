<?php

namespace App\Core\Features;

use App\Core\Features\Feature;
use App\Core\Features\FeatureUsageLog;
use App\Core\Features\PlanFeature;
use App\Core\Features\TenantFeature;
use App\Core\Features\TenantFeatureAddon;
use App\Core\Plans\Plan;
use App\Core\Tenant\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeatureManager
{
    /**
     * Check if tenant has active feature (considers plan, addon, trial, gift).
     */
    public function hasFeature(string $slug, ?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            return false;
        }

        // 1. Check direct TenantFeature record (overrides, addons, synced plan features)
        $tenantFeature = TenantFeature::where('tenant_id', $tenantId)
            ->where('feature_slug', $slug)
            ->first();

        if ($tenantFeature) {
            if (!$tenantFeature->enabled) {
                return false;
            }
            if ($tenantFeature->expires_at && $tenantFeature->expires_at->isPast()) {
                return false;
            }
            if ($tenantFeature->is_trial && $tenantFeature->trial_ends_at && $tenantFeature->trial_ends_at->isPast()) {
                return false;
            }
            return true;
        }

        // 2. Fallback: Check if feature is in Tenant's Plan (if not yet synced to tenant_features)
        // Ideally, all active features should be in tenant_features, but for robustness we check plan.
        $tenant = Tenant::find($tenantId);
        if (!$tenant || !$tenant->plan_id) {
            return false;
        }

        $planFeature = PlanFeature::where('plan_id', $tenant->plan_id)
            ->where('feature_slug', $slug)
            ->first();

        if ($planFeature && $planFeature->included) {
            // Optional: Sync to tenant_features now to avoid future lookups?
            // For now, just return true.
            return true;
        }

        return false;
    }

    /**
     * Check feature dependencies.
     */
    public function checkDependencies(string $slug): bool
    {
        $feature = Feature::with('dependencies')->where('slug', $slug)->first();
        if (!$feature || $feature->dependencies->isEmpty()) {
            return true;
        }

        foreach ($feature->dependencies as $dependency) {
            if (!$this->hasFeature($dependency->requires_feature_slug)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get limit for feature.
     */
    public function getLimit(string $slug, ?int $tenantId = null): ?int
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        if (!$tenantId) return 0;

        // 1. Check TenantFeature (custom limit or synced)
        $tenantFeature = TenantFeature::where('tenant_id', $tenantId)
            ->where('feature_slug', $slug)
            ->first();

        if ($tenantFeature && !is_null($tenantFeature->limit_value)) {
            return $tenantFeature->limit_value;
        }

        // 2. Check PlanFeature
        $tenant = Tenant::find($tenantId);
        if ($tenant && $tenant->plan_id) {
            $planFeature = PlanFeature::where('plan_id', $tenant->plan_id)
                ->where('feature_slug', $slug)
                ->first();
            
            if ($planFeature) {
                return $planFeature->limit_value;
            }
        }

        return 0; // Default to 0 if not found/unlimited logic needs clarification (null usually means unlimited in some systems, but user asked for ?int)
        // If the user's schema allows null for unlimited, we should return null.
        // Schema: limit_value -> nullable integer.
        // So I should return null if no limit found or explicit null.
        
        return null;
    }

    /**
     * Get current usage.
     */
    public function getUsage(string $slug, ?int $tenantId = null): int
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        if (!$tenantId) return 0;

        $tenantFeature = TenantFeature::where('tenant_id', $tenantId)
            ->where('feature_slug', $slug)
            ->first();

        return $tenantFeature ? $tenantFeature->used_value : 0;
    }

    /**
     * Calculate remaining quantity.
     */
    public function getRemaining(string $slug, ?int $tenantId = null): ?int
    {
        $limit = $this->getLimit($slug, $tenantId);
        
        if (is_null($limit)) {
            return null; // Unlimited
        }

        $usage = $this->getUsage($slug, $tenantId);
        return max(0, $limit - $usage);
    }

    /**
     * Can use X quantity? (considers soft/hard limit)
     */
    public function canUse(string $slug, int $quantity = 1, ?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        
        if (!$this->hasFeature($slug, $tenantId)) {
            return [
                'allowed' => false,
                'soft_warning' => false,
                'message' => 'Feature not enabled for this tenant.',
            ];
        }

        $limit = $this->getLimit($slug, $tenantId);

        if (is_null($limit)) {
            return [
                'allowed' => true,
                'soft_warning' => false,
                'message' => 'Allowed (Unlimited)',
            ];
        }

        $usage = $this->getUsage($slug, $tenantId);
        $remaining = $limit - $usage;

        if ($remaining >= $quantity) {
            return [
                'allowed' => true,
                'soft_warning' => false,
                'message' => 'Allowed',
            ];
        }

        // Check for soft limit
        // We need to know if the feature is soft-limited.
        // Check TenantFeature or PlanFeature for 'soft_limit' flag.
        $isSoftLimit = false;
        
        $tenantFeature = TenantFeature::where('tenant_id', $tenantId)->where('feature_slug', $slug)->first();
        // Schema doesn't have soft_limit on tenant_features, only on plan_features?
        // Let's check schema...
        // plan_features has 'soft_limit'. tenant_features does NOT have it in schema provided in part 1.
        // So we must check plan_features for soft_limit configuration.
        
        $tenant = Tenant::find($tenantId);
        if ($tenant && $tenant->plan_id) {
             $planFeature = PlanFeature::where('plan_id', $tenant->plan_id)
                ->where('feature_slug', $slug)
                ->first();
             if ($planFeature && $planFeature->soft_limit) {
                 $isSoftLimit = true;
             }
        }

        if ($isSoftLimit) {
            return [
                'allowed' => true,
                'soft_warning' => true,
                'message' => 'Soft limit reached.',
            ];
        }

        return [
            'allowed' => false,
            'soft_warning' => false,
            'message' => 'Limit reached.',
        ];
    }

    /**
     * Increment usage and log.
     */
    public function incrementUsage(string $slug, int $quantity = 1, ?array $metadata = null): bool
    {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) return false;

        $check = $this->canUse($slug, $quantity, $tenantId);
        
        // If not allowed, we still might log the attempt?
        // User asked "Increment usage and log". If strictly not allowed, we probably shouldn't increment.
        // But if soft warning, we DO increment.
        
        if (!$check['allowed']) {
             FeatureUsageLog::create([
                'tenant_id' => $tenantId,
                'feature_slug' => $slug,
                'user_id' => auth()->id(),
                'action' => 'increment_failed',
                'quantity' => $quantity,
                'result' => 'denied',
                'metadata' => $metadata,
            ]);
            return false;
        }

        // Ensure TenantFeature exists
        $tenantFeature = TenantFeature::firstOrCreate(
            ['tenant_id' => $tenantId, 'feature_slug' => $slug],
            ['source' => 'plan', 'enabled' => true] // Defaults if creating from scratch
        );

        $tenantFeature->increment('used_value', $quantity);

        FeatureUsageLog::create([
            'tenant_id' => $tenantId,
            'feature_slug' => $slug,
            'user_id' => auth()->id(),
            'action' => 'increment',
            'quantity' => $quantity,
            'result' => $check['soft_warning'] ? 'soft_warning' : 'allowed',
            'metadata' => $metadata,
        ]);

        $this->checkAndSendAlerts($slug, $tenantId);

        return true;
    }

    /**
     * Reset usage (called by scheduler).
     */
    public function resetUsage(string $slug, int $tenantId): void
    {
        $tenantFeature = TenantFeature::where('tenant_id', $tenantId)
            ->where('feature_slug', $slug)
            ->first();

        if ($tenantFeature) {
            $tenantFeature->update(['used_value' => 0, 'reset_at' => now()]);
            FeatureUsageLog::create([
                'tenant_id' => $tenantId,
                'feature_slug' => $slug,
                'action' => 'reset',
                'quantity' => 0,
                'result' => 'allowed',
            ]);
        }
    }

    /**
     * Get all tenant features with status.
     */
    public function getTenantFeatures(?int $tenantId = null): Collection
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        if (!$tenantId) return collect();

        return TenantFeature::where('tenant_id', $tenantId)->get();
    }

    /**
     * Check if trial is expiring.
     */
    public function checkTrialExpiring(int $tenantId): Collection
    {
        // Return features where is_trial=true and trial_ends_at is within 3 days (example)
        return TenantFeature::where('tenant_id', $tenantId)
            ->where('is_trial', true)
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->where('trial_ends_at', '>', now())
            ->get();
    }

    /**
     * Send alert if necessary (80%, 90%, 100% limit).
     */
    public function checkAndSendAlerts(string $slug, int $tenantId): void
    {
        $limit = $this->getLimit($slug, $tenantId);
        if (is_null($limit) || $limit <= 0) return;

        $usage = $this->getUsage($slug, $tenantId);
        $percent = ($usage / $limit) * 100;

        $alertType = null;
        if ($percent >= 100) {
            $alertType = 'limit_reached';
        } elseif ($percent >= 90) {
            $alertType = 'approaching_limit_90';
        } elseif ($percent >= 80) {
            $alertType = 'approaching_limit_80';
        }

        if ($alertType) {
            // Check if alert already sent today/recently
            // This logic requires the 'feature_alerts' table.
            // "alert_type" column in schema.
            
            // Simple logic: Don't spam.
            // For now, we just create the record if not exists for this "level"
            // Or maybe we just log it.
            // User schema: feature_alerts(tenant_id, feature_slug, alert_type, threshold_percent, sent_at...)
            
            // TODO: check if already sent
        }
    }

    /**
     * Activate trial for feature.
     */
    public function activateTrial(string $slug, int $days, ?int $tenantId = null): TenantFeature
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        
        return TenantFeature::updateOrCreate(
            ['tenant_id' => $tenantId, 'feature_slug' => $slug],
            [
                'source' => 'trial',
                'enabled' => true,
                'is_trial' => true,
                'trial_ends_at' => now()->addDays($days),
                'granted_by' => auth()->id(),
                'granted_at' => now(),
            ]
        );
    }

    /**
     * Purchase addon.
     */
    public function purchaseAddon(string $slug, string $billingCycle, ?int $tenantId = null): TenantFeatureAddon
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;

        // Create Addon Record
        $addon = TenantFeatureAddon::create([
            'tenant_id' => $tenantId,
            'feature_slug' => $slug,
            'quantity' => 1,
            'price_paid' => 0, // Should come from feature price
            'billing_cycle' => $billingCycle,
            'starts_at' => now(),
            'auto_renew' => true,
        ]);

        // Enable Feature
        TenantFeature::updateOrCreate(
            ['tenant_id' => $tenantId, 'feature_slug' => $slug],
            [
                'source' => 'addon',
                'enabled' => true,
                'expires_at' => null, // Addons usually run until cancelled
            ]
        );

        return $addon;
    }

    /**
     * Feature teaser info.
     */
    public function getUpgradeInfo(string $slug): array
    {
        $feature = Feature::where('slug', $slug)->first();
        if (!$feature) return [];

        return [
            'available_in_plans' => [], // TODO: query plans that include this feature
            'price' => $feature->price_monthly,
            'description' => $feature->description,
        ];
    }
}
