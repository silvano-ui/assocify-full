<?php

namespace Modules\WhiteLabel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\WhiteLabel\Services\BrandingService;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\View;

class ApplyTenantBranding
{
    protected BrandingService $brandingService;

    public function __construct(BrandingService $brandingService)
    {
        $this->brandingService = $brandingService;
    }

    public function handle(Request $request, Closure $next)
    {
        $tenantId = auth()->user()?->tenant_id ?? session('tenant_id');

        if ($tenantId) {
            // Share branding with views
            $branding = $this->brandingService->getBranding($tenantId);
            View::share('tenantBranding', $branding);
            View::share('tenantColors', $this->brandingService->getColors($tenantId));

            // Inject CSS variables
            // We can push this to a stack or share a variable that layout renders
            View::share('tenantCssVariables', $this->brandingService->generateCssVariables($tenantId));

            // Filament specific configuration override
            // This might need to happen in a ServiceProvider or before Filament boots,
            // but middleware can sometimes tweak runtime config.
            // Filament V3 uses Panels and Colors classes, might need dynamic registration.
            // For now, we assume simple config override or view-based application.
        }

        return $next($request);
    }
}
