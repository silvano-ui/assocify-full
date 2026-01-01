<?php

namespace App\Core\ModuleManager;

use Illuminate\Support\Facades\File;
use App\Core\Tenant\Tenant;

class ModuleManager
{
    protected $modulesPath;

    public function __construct()
    {
        $this->modulesPath = base_path('Modules');
    }

    public function getAvailableModules()
    {
        $modules = [];
        if (!File::exists($this->modulesPath)) {
            return $modules;
        }

        $directories = File::directories($this->modulesPath);

        foreach ($directories as $directory) {
            $moduleJson = $directory . '/module.json';
            if (File::exists($moduleJson)) {
                $module = json_decode(File::get($moduleJson), true);
                if ($module) {
                    $modules[$module['slug']] = $module;
                }
            }
        }

        return $modules;
    }

    public function isEnabledForTenant(string $moduleSlug, Tenant $tenant): bool
    {
        // Check if enabled in tenant_modules
        $tenantModule = $tenant->modules()->where('module_slug', $moduleSlug)->first();
        
        if ($tenantModule && $tenantModule->enabled) {
             return true;
        }

        // Check if included in plan
        $plan = $tenant->plan;
        if ($plan && is_array($plan->modules) && in_array($moduleSlug, $plan->modules)) {
             return true;
        }

        return false;
    }
}
