<?php

namespace Modules\WhiteLabel\Services;

use Modules\WhiteLabel\Entities\TenantBranding;
use Illuminate\Support\Facades\Storage;

class BrandingService
{
    public function getBranding(?int $tenantId = null): ?TenantBranding
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        if (!$tenantId) return null;

        return TenantBranding::where('tenant_id', $tenantId)->first();
    }

    public function getColors(int $tenantId): array
    {
        $branding = $this->getBranding($tenantId);
        
        if (!$branding) {
            return config('whitelabel.default_colors', [
                'primary' => '#3B82F6',
                'secondary' => '#1E40AF',
                'accent' => '#F59E0B',
                'success' => '#10B981',
                'warning' => '#F59E0B',
                'danger' => '#EF4444',
                'background' => '#F3F4F6',
                'sidebar' => '#1F2937',
                'text' => '#111827',
            ]);
        }

        return [
            'primary' => $branding->primary_color,
            'secondary' => $branding->secondary_color,
            'accent' => $branding->accent_color,
            'success' => $branding->success_color,
            'warning' => $branding->warning_color,
            'danger' => $branding->danger_color,
            'background' => $branding->background_color,
            'sidebar' => $branding->sidebar_color,
            'text' => $branding->text_color,
        ];
    }

    public function getLogo(int $tenantId, bool $dark = false): ?string
    {
        $branding = $this->getBranding($tenantId);
        if (!$branding) return null;

        $path = $dark ? $branding->logo_dark_path : $branding->logo_path;
        return $path ? Storage::url($path) : null;
    }

    public function getFavicon(int $tenantId): ?string
    {
        $branding = $this->getBranding($tenantId);
        return $branding && $branding->favicon_path ? Storage::url($branding->favicon_path) : null;
    }

    public function getCustomCss(int $tenantId): ?string
    {
        return $this->getBranding($tenantId)?->custom_css;
    }

    public function getCustomJs(int $tenantId): ?string
    {
        return $this->getBranding($tenantId)?->custom_js;
    }

    public function getThemeMode(int $tenantId): string
    {
        return $this->getBranding($tenantId)?->theme_mode ?? 'auto';
    }

    public function applyPreset(int $tenantId, string $presetSlug): bool
    {
        // Implementation for presets would go here
        // For now, return false as presets are not defined in prompt
        return false;
    }

    public function generateCssVariables(int $tenantId): string
    {
        $colors = $this->getColors($tenantId);
        
        $css = ":root {\n";
        foreach ($colors as $key => $value) {
            $css .= "  --color-{$key}: {$value};\n";
            // Also generate RGB values for Tailwind opacity support if needed
            // $css .= "  --color-{$key}-rgb: " . $this->hexToRgb($value) . ";\n";
        }
        $css .= "}\n";

        return $css;
    }
}
