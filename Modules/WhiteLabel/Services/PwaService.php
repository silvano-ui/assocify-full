<?php

namespace Modules\WhiteLabel\Services;

use Modules\WhiteLabel\Entities\TenantPwaSetting;
use Illuminate\Support\Facades\Storage;

class PwaService
{
    public function getManifest(int $tenantId): array
    {
        $settings = TenantPwaSetting::where('tenant_id', $tenantId)->first();
        
        if (!$settings) {
            return []; // Return default manifest or empty
        }

        return [
            'name' => $settings->app_name,
            'short_name' => $settings->short_name,
            'description' => $settings->description,
            'start_url' => $settings->start_url ?? '/',
            'display' => $settings->display_mode,
            'background_color' => $settings->background_color,
            'theme_color' => $settings->theme_color,
            'orientation' => $settings->orientation,
            'scope' => $settings->scope ?? '/',
            'icons' => $this->getIcons($tenantId),
        ];
    }

    public function getIcons(int $tenantId): array
    {
        $settings = TenantPwaSetting::where('tenant_id', $tenantId)->first();
        if (!$settings) return [];

        $icons = [];
        $sizes = ['72', '96', '128', '144', '152', '192', '384', '512'];

        foreach ($sizes as $size) {
            $field = "icon_{$size}";
            if ($settings->$field) {
                $icons[] = [
                    'src' => Storage::url($settings->$field),
                    'sizes' => "{$size}x{$size}",
                    'type' => 'image/png',
                ];
            }
        }

        return $icons;
    }

    public function getServiceWorkerConfig(int $tenantId): array
    {
        // Return config for SW generation
        return [];
    }
}
