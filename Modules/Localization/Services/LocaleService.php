<?php

namespace Modules\Localization\Services;

use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Modules\Localization\Entities\Language;

class LocaleService
{
    protected $languageService;
    protected $tenantLanguageService;

    public function __construct(LanguageService $languageService, TenantLanguageService $tenantLanguageService)
    {
        $this->languageService = $languageService;
        $this->tenantLanguageService = $tenantLanguageService;
    }

    public function getCurrentLocale(): string
    {
        // 1. Check Session
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 2. Check User Preference
        if (Auth::check() && Auth::user()->locale) {
            return Auth::user()->locale;
        }

        // 3. Check Tenant Default
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenantLocale = $this->tenantLanguageService->getTenantDefaultLanguage(Auth::user()->tenant_id);
            if ($tenantLocale) {
                return $tenantLocale->code;
            }
        }
        
        // 4. Check Platform Default
        $platformDefault = $this->languageService->getDefaultLanguage();
        if ($platformDefault) {
            return $platformDefault->code;
        }

        // 5. Hard Fallback
        return 'it';
    }

    public function setLocale(string $locale): void
    {
        if ($this->languageService->getLanguageByCode($locale)) {
            Session::put('locale', $locale);
            app()->setLocale($locale);
        }
    }

    public function getUserLocale(int $userId): ?string
    {
        $user = User::find($userId);
        return $user ? $user->locale : null;
    }

    public function setUserLocale(int $userId, string $locale): bool
    {
        $user = User::find($userId);
        if (!$user) return false;

        $user->locale = $locale;
        return $user->save();
    }

    public function detectBrowserLocale(Request $request): string
    {
        $locale = $request->getPreferredLanguage();
        // Extract first two chars 'en-US' -> 'en'
        return substr($locale, 0, 2);
    }

    public function formatDate($date, string $locale): string
    {
        // Placeholder for Carbon/Intl date formatting
        return \Carbon\Carbon::parse($date)->locale($locale)->isoFormat('LL');
    }

    public function formatNumber($number, string $locale, int $decimals = 2): string
    {
        return number_format($number, $decimals, 
            $locale === 'it' ? ',' : '.', 
            $locale === 'it' ? '.' : ','
        );
    }

    public function formatCurrency($amount, string $locale, string $currency = 'EUR'): string
    {
        $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($amount, $currency);
    }
}
