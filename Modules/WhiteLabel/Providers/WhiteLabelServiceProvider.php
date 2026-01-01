<?php

namespace Modules\WhiteLabel\Providers;

use Illuminate\Support\ServiceProvider;

class WhiteLabelServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'WhiteLabel';
    protected string $moduleNameLower = 'whitelabel';

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $configPath = module_path($this->moduleName, 'config/config.php');
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, $this->moduleNameLower);
        }
    }

    protected function registerViews(): void
    {
        $viewPath = module_path($this->moduleName, 'resources/views');
        $this->loadViewsFrom($viewPath, $this->moduleNameLower);
    }
}
