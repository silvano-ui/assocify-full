<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasFeature(string $slug, ?int $tenantId = null)
 * @method static bool checkDependencies(string $slug)
 * @method static int|null getLimit(string $slug, ?int $tenantId = null)
 * @method static int getUsage(string $slug, ?int $tenantId = null)
 * @method static int|null getRemaining(string $slug, ?int $tenantId = null)
 * @method static array canUse(string $slug, int $quantity = 1, ?int $tenantId = null)
 * @method static bool incrementUsage(string $slug, int $quantity = 1, ?array $metadata = null)
 * @method static void resetUsage(string $slug, int $tenantId)
 * @method static \Illuminate\Support\Collection getTenantFeatures(?int $tenantId = null)
 * @method static \Illuminate\Support\Collection checkTrialExpiring(int $tenantId)
 * @method static void checkAndSendAlerts(string $slug, int $tenantId)
 * @method static \App\Core\Features\TenantFeature activateTrial(string $slug, int $days, ?int $tenantId = null)
 * @method static \App\Core\Features\TenantFeatureAddon purchaseAddon(string $slug, string $billingCycle, ?int $tenantId = null)
 * @method static array getUpgradeInfo(string $slug)
 * 
 * @see \App\Core\Features\FeatureManager
 */
class Features extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'features';
    }
}
