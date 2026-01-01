<?php

namespace Modules\WhiteLabel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\WhiteLabel\Services\BrandingService;
use Illuminate\Support\Facades\View;

class InjectCustomAssets
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
            $customCss = $this->brandingService->getCustomCss($tenantId);
            $customJs = $this->brandingService->getCustomJs($tenantId);

            if ($customCss) {
                View::share('tenantCustomCss', $customCss);
            }
            if ($customJs) {
                View::share('tenantCustomJs', $customJs);
            }
        }

        return $next($request);
    }
}
