<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\TenantTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    public function get(string $key, string $locale, string $group = 'messages', ?int $tenantId = null): ?string
    {
        // Cache key strategy: translation:{tenant_id}:{locale}:{group}:{key}
        $cacheKey = "translation:{$tenantId}:{$locale}:{$group}:{$key}";

        return Cache::remember($cacheKey, 60, function () use ($key, $locale, $group, $tenantId) {
            // 1. Check Tenant Override
            if ($tenantId) {
                $tenantTranslation = TenantTranslation::where('tenant_id', $tenantId)
                    ->where('locale', $locale)
                    ->where('group', $group)
                    ->where('key', $key)
                    ->first();

                if ($tenantTranslation && $tenantTranslation->value) {
                    return $tenantTranslation->value;
                }
            }

            // 2. Check Base Translation
            $translation = Translation::where('locale', $locale)
                ->where('group', $group)
                ->where('key', $key)
                ->first();

            return $translation ? $translation->value : $key;
        });
    }

    public function set(string $key, ?string $value, string $locale, string $group = 'messages'): Translation
    {
        $translation = Translation::updateOrCreate(
            ['locale' => $locale, 'group' => $group, 'key' => $key],
            ['value' => $value]
        );

        // Clear cache
        $this->clearCache($key, $locale, $group);

        return $translation;
    }

    public function setTenantOverride(string $key, ?string $value, string $locale, string $group, int $tenantId): TenantTranslation
    {
        $translation = TenantTranslation::updateOrCreate(
            ['tenant_id' => $tenantId, 'locale' => $locale, 'group' => $group, 'key' => $key],
            ['value' => $value]
        );

        // Clear cache
        $this->clearCache($key, $locale, $group, $tenantId);

        return $translation;
    }

    public function importTranslations(string $locale, array $data, string $group = 'messages'): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Flatten dot notation if needed, for now assume simple key-value or handle recursively
                // Skipping recursive for brevity, assuming flattened input or simple groups
                continue; 
            }
            $this->set($key, $value, $locale, $group);
        }
    }

    public function export(string $locale, string $group = 'messages'): array
    {
        return Translation::where('locale', $locale)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function getGroups(): array
    {
        return Translation::distinct('group')->pluck('group')->toArray();
    }

    public function getMissingTranslations(string $locale): Collection
    {
        // Assuming 'en' is the source of truth
        $sourceKeys = Translation::where('locale', 'en')->get(['group', 'key']);
        
        $missing = collect();
        
        foreach ($sourceKeys as $source) {
            $exists = Translation::where('locale', $locale)
                ->where('group', $source->group)
                ->where('key', $source->key)
                ->exists();
                
            if (!$exists) {
                $missing->push($source);
            }
        }
        
        return $missing;
    }

    public function getUnreviewedTranslations(string $locale): Collection
    {
        return Translation::where('locale', $locale)
            ->autoTranslated()
            ->unreviewed()
            ->get();
    }

    private function clearCache(string $key, string $locale, string $group, ?int $tenantId = null): void
    {
        if ($tenantId) {
             Cache::forget("translation:{$tenantId}:{$locale}:{$group}:{$key}");
        }
        // Also clear base cache if needed, or implement tag-based clearing
        Cache::forget("translation::{$locale}:{$group}:{$key}"); // Base translation cache
    }
}
