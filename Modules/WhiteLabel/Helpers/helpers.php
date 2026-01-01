<?php

if (!function_exists('tenant_branding')) {
    function tenant_branding(): ?\Modules\WhiteLabel\Entities\TenantBranding
    {
        $service = app(\Modules\WhiteLabel\Services\BrandingService::class);
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');
        return $tenantId ? $service->getBranding($tenantId) : null;
    }
}

if (!function_exists('tenant_color')) {
    function tenant_color(string $key): string
    {
        $service = app(\Modules\WhiteLabel\Services\BrandingService::class);
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');
        if (!$tenantId) return config("whitelabel.default_colors.{$key}", '#000000');
        
        $colors = $service->getColors($tenantId);
        return $colors[$key] ?? config("whitelabel.default_colors.{$key}", '#000000');
    }
}

if (!function_exists('tenant_logo')) {
    function tenant_logo(bool $dark = false): ?string
    {
        $service = app(\Modules\WhiteLabel\Services\BrandingService::class);
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');
        return $tenantId ? $service->getLogo($tenantId, $dark) : null;
    }
}

if (!function_exists('tenant_domain')) {
    function tenant_domain(): ?string
    {
        $service = app(\Modules\WhiteLabel\Services\DomainService::class);
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');
        return $tenantId ? $service->getPrimaryDomain($tenantId) : null;
    }
}
