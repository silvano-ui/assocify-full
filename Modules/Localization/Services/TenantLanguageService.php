<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\TenantLanguage;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Tenant;

class TenantLanguageService
{
    public function getAvailableLanguages(int $tenantId): Collection
    {
        return TenantLanguage::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with('language')
            ->get()
            ->pluck('language');
    }

    public function getTenantDefaultLanguage(int $tenantId): ?Language
    {
        $tenantLanguage = TenantLanguage::where('tenant_id', $tenantId)
            ->where('is_default', true)
            ->with('language')
            ->first();

        return $tenantLanguage ? $tenantLanguage->language : null;
    }

    public function setTenantDefaultLanguage(int $tenantId, string $code): bool
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            return false;
        }

        TenantLanguage::where('tenant_id', $tenantId)->update(['is_default' => false]);

        return (bool) TenantLanguage::updateOrCreate(
            ['tenant_id' => $tenantId, 'language_id' => $language->id],
            ['is_default' => true, 'is_active' => true]
        );
    }

    public function enableLanguage(int $tenantId, string $code): bool
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            return false;
        }

        return (bool) TenantLanguage::updateOrCreate(
            ['tenant_id' => $tenantId, 'language_id' => $language->id],
            ['is_active' => true]
        );
    }

    public function disableLanguage(int $tenantId, string $code): bool
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            return false;
        }

        // Prevent disabling default language
        $tenantLanguage = TenantLanguage::where('tenant_id', $tenantId)
            ->where('language_id', $language->id)
            ->first();

        if ($tenantLanguage && $tenantLanguage->is_default) {
            return false;
        }

        return (bool) TenantLanguage::where('tenant_id', $tenantId)
            ->where('language_id', $language->id)
            ->update(['is_active' => false]);
    }

    public function syncLanguages(int $tenantId, array $codes): void
    {
        $languages = Language::whereIn('code', $codes)->get();
        
        // Disable all first (except default maybe? for now strict sync)
        // Better approach: ensure all provided codes are enabled, others disabled
        
        // Get all language IDs
        $allLanguageIds = Language::pluck('id')->toArray();
        $targetLanguageIds = $languages->pluck('id')->toArray();
        
        foreach ($allLanguageIds as $langId) {
            $isActive = in_array($langId, $targetLanguageIds);
            
            TenantLanguage::updateOrCreate(
                ['tenant_id' => $tenantId, 'language_id' => $langId],
                ['is_active' => $isActive]
            );
        }
    }
}
