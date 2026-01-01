<?php

namespace Modules\WhiteLabel\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\WhiteLabel\Services\BrandingService;
use Modules\WhiteLabel\Services\DomainService;
use Modules\WhiteLabel\Services\WhmcsApiService;
use Modules\WhiteLabel\Services\EmailBrandingService;
use Modules\WhiteLabel\Services\PwaService;
use Modules\WhiteLabel\Services\PdfBrandingService;
use Modules\WhiteLabel\Services\MenuService;

class WhiteLabelServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'WhiteLabel';
    protected string $name = 'WhiteLabel';
    protected string $moduleNameLower = 'whitelabel';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        // Register Services
        $this->app->singleton(BrandingService::class);
        $this->app->singleton(DomainService::class);
        
        $this->app->singleton(WhmcsApiService::class, function ($app) {
            return new WhmcsApiService(
                config('whitelabel.whmcs_api_url', ''),
                config('whitelabel.whmcs_api_identifier', ''),
                config('whitelabel.whmcs_api_secret', '')
            );
        });

        $this->app->singleton(EmailBrandingService::class);
        $this->app->singleton(PwaService::class);
        $this->app->singleton(PdfBrandingService::class);
        $this->app->singleton(MenuService::class);

        // Load Helpers
        $this->loadHelpers();
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->name, 'config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->mapViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            BrandingService::class,
            DomainService::class,
            WhmcsApiService::class,
            EmailBrandingService::class,
            PwaService::class,
            PdfBrandingService::class,
            MenuService::class,
        ];
    }

    private function mapViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    protected function loadHelpers(): void
    {
        $helpersPath = module_path($this->name, 'Helpers/helpers.php');
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }
    }
}
