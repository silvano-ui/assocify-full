<?php

namespace Modules\WhiteLabel\Services;

use Modules\WhiteLabel\Entities\TenantDomain;
use Illuminate\Support\Str;

class DomainService
{
    public function getDomainForTenant(int $tenantId): ?TenantDomain
    {
        return TenantDomain::where('tenant_id', $tenantId)
            ->where('is_primary', true)
            ->first() ?? TenantDomain::where('tenant_id', $tenantId)->first();
    }

    public function getPrimaryDomain(int $tenantId): ?string
    {
        return $this->getDomainForTenant($tenantId)?->domain;
    }

    public function verifyDomain(TenantDomain $domain): bool
    {
        if ($domain->is_verified) return true;

        if ($this->checkDnsVerification($domain)) {
            $domain->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    public function generateVerificationToken(): string
    {
        return 'assocify-verify-' . Str::random(32);
    }

    public function checkDnsVerification(TenantDomain $domain): bool
    {
        if (!$domain->verification_token) return false;

        $records = @dns_get_record($domain->domain, DNS_TXT);
        if (!$records) return false;

        foreach ($records as $record) {
            if (isset($record['txt']) && $record['txt'] === $domain->verification_token) {
                return true;
            }
        }

        return false;
    }

    public function getSubdomain(int $tenantId): string
    {
        // Assuming Tenant model has a 'slug' field.
        // If not, we might need to fetch it differently or use ID.
        // Let's assume we can get it from the tenant relation if needed,
        // but for now, let's look up the Tenant.
        $tenant = \App\Core\Tenant\Tenant::find($tenantId);
        $suffix = config('whitelabel.default_subdomain_suffix', '.assocify.app');
        
        return ($tenant->slug ?? 'tenant-' . $tenantId) . $suffix;
    }
}
