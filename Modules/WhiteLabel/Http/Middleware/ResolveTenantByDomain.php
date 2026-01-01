<?php

namespace Modules\WhiteLabel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\WhiteLabel\Services\DomainService;
use App\Core\Tenant\Tenant;

class ResolveTenantByDomain
{
    protected DomainService $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Logic to find tenant by domain
        // 1. Check Custom Domains
        $tenantDomain = \Modules\WhiteLabel\Entities\TenantDomain::where('domain', $host)
            ->where('is_verified', true)
            ->first();

        if ($tenantDomain) {
            $this->setTenant($tenantDomain->tenant_id);
            return $next($request);
        }

        // 2. Check Subdomains
        // Assuming we can parse subdomain from host and match against tenant slug or ID
        // Simplified check:
        // if host ends with .assocify.app (config-based)
        $suffix = config('whitelabel.default_subdomain_suffix', '.assocify.app');
        if (str_ends_with($host, $suffix)) {
            $slug = substr($host, 0, -strlen($suffix));
            $tenant = Tenant::where('slug', $slug)->first();
            if ($tenant) {
                $this->setTenant($tenant->id);
                return $next($request);
            }
        }

        // If strict mode, redirect or abort. 
        // For now, let it pass or maybe set default tenant?
        // If not found, do nothing, app might rely on auth user tenant or default.
        
        return $next($request);
    }

    protected function setTenant($tenantId)
    {
        // Set in session/config/context
        session(['tenant_id' => $tenantId]);
        // Potentially set DB connection or scoped global
    }
}
