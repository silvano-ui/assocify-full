<?php

namespace Modules\Localization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Localization\Entities\Language;
use Carbon\Carbon;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Carbon::setLocale($locale);
        
        // Ensure session has the locale (useful for persistence across requests if logic changes)
        if (Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }

    protected function resolveLocale(Request $request): string
    {
        // 1. User Preference (if logged in)
        if (Auth::check() && !empty(Auth::user()->locale)) {
            return Auth::user()->locale;
        }

        // 2. Session (if manually switched)
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 3. Tenant Default (if in tenant context)
        // Assuming 'filament.tenant' is available in request or via helper if we are in a tenant panel.
        // Or if using a specific tenancy package, use that.
        // For Filament Multi-Tenancy:
        if (function_exists('filament') && filament()->getCurrentPanel() && filament()->getTenant()) {
            $tenant = filament()->getTenant();
             if (!empty($tenant->default_locale)) {
                return $tenant->default_locale;
            }
        }

        // 4. Platform Default
        // Cache this query to avoid DB hit on every request
        $platformDefault = cache()->remember('platform_default_locale', 3600, function () {
            return Language::where('is_default', true)->value('code');
        });

        if ($platformDefault) {
            return $platformDefault;
        }

        // 5. Fallback
        return config('app.fallback_locale', 'it');
    }
}
