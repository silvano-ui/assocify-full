<?php

namespace Modules\WhiteLabel\Services;

use Modules\WhiteLabel\Entities\TenantEmailBranding;

class EmailBrandingService
{
    public function getEmailConfig(int $tenantId): array
    {
        $branding = TenantEmailBranding::where('tenant_id', $tenantId)->first();
        
        if (!$branding) {
            return [];
        }

        return $branding->toArray();
    }

    public function getEmailHeader(int $tenantId): ?string
    {
        return TenantEmailBranding::where('tenant_id', $tenantId)->value('email_header_html');
    }

    public function getEmailFooter(int $tenantId): ?string
    {
        return TenantEmailBranding::where('tenant_id', $tenantId)->value('email_footer_html');
    }

    public function getFromAddress(int $tenantId): array
    {
        $branding = TenantEmailBranding::where('tenant_id', $tenantId)->first();

        if ($branding) {
            return [
                'name' => $branding->from_name ?? config('mail.from.name'),
                'email' => $branding->from_email ?? config('mail.from.address'),
                'reply_to' => $branding->reply_to,
            ];
        }

        return [
            'name' => config('mail.from.name'),
            'email' => config('mail.from.address'),
            'reply_to' => null,
        ];
    }

    public function hasCustomSmtp(int $tenantId): bool
    {
        // Logic to check if tenant has custom SMTP settings.
        // Assuming this might be stored in a separate settings table or json field not fully detailed yet,
        // or within email_branding if fields existed.
        // For now, returning false as standard table doesn't have SMTP fields explicitly listed in previous prompts
        // (only email_template_overrides etc).
        // If we need to support it, we'd check a 'smtp_config' field or similar.
        return false;
    }

    public function getSmtpConfig(int $tenantId): ?array
    {
        if (!$this->hasCustomSmtp($tenantId)) {
            return null;
        }
        
        // Return custom SMTP config
        return [];
    }
}
