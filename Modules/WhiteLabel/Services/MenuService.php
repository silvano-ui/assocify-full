<?php

namespace Modules\WhiteLabel\Services;

use Modules\WhiteLabel\Entities\TenantMenuSetting;

class MenuService
{
    public function getMenuItems(int $tenantId): array
    {
        $settings = TenantMenuSetting::where('tenant_id', $tenantId)->first();
        return $settings ? ($settings->custom_menu_items ?? []) : [];
    }

    public function getHiddenModules(int $tenantId): array
    {
        $settings = TenantMenuSetting::where('tenant_id', $tenantId)->first();
        return $settings ? ($settings->hidden_modules ?? []) : [];
    }

    public function getDashboardWidgets(int $tenantId): array
    {
        $settings = TenantMenuSetting::where('tenant_id', $tenantId)->first();
        return $settings ? ($settings->dashboard_widgets ?? []) : [];
    }

    public function getQuickActions(int $tenantId): array
    {
        $settings = TenantMenuSetting::where('tenant_id', $tenantId)->first();
        return $settings ? ($settings->quick_actions ?? []) : [];
    }

    public function isModuleVisible(int $tenantId, string $module): bool
    {
        $hidden = $this->getHiddenModules($tenantId);
        return !in_array($module, $hidden);
    }
}
