<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\TranslationSetting;
use Exception;

class AutoTranslateService
{
    protected $deepLService;
    protected $libreTranslateService;

    public function __construct(DeepLService $deepLService, LibreTranslateService $libreTranslateService)
    {
        $this->deepLService = $deepLService;
        $this->libreTranslateService = $libreTranslateService;
    }

    public function translate(string $text, string $from, string $to, string $provider = 'deepl', ?int $tenantId = null): ?string
    {
        $settings = $this->getSettings($tenantId, $provider);
        
        if (!$settings || !$settings->is_active) {
            // Try fallback or return null
            return null;
        }

        if (!$settings->hasQuotaRemaining(strlen($text))) {
            return null; // Quota exceeded
        }

        $translated = null;

        if ($provider === 'deepl') {
            $this->deepLService->setApiKey($settings->api_key);
            $translated = $this->deepLService->translate($text, $from, $to);
        } elseif ($provider === 'libretranslate') {
            // Re-instantiate or configure LibreTranslate with settings
             $libre = new LibreTranslateService($settings->api_url, $settings->api_key);
             $translated = $libre->translate($text, $from, $to);
        }

        if ($translated) {
            $settings->incrementUsage(strlen($text));
        }

        return $translated;
    }

    public function translateBatch(array $texts, string $from, string $to, string $provider = 'deepl', ?int $tenantId = null): array
    {
        $settings = $this->getSettings($tenantId, $provider);
        if (!$settings || !$settings->is_active) return [];
        
        $totalChars = array_sum(array_map('strlen', $texts));
        if (!$settings->hasQuotaRemaining($totalChars)) return [];

        $results = [];

        if ($provider === 'deepl') {
             $this->deepLService->setApiKey($settings->api_key);
             $results = $this->deepLService->translateBatch($texts, $from, $to);
        } elseif ($provider === 'libretranslate') {
             $libre = new LibreTranslateService($settings->api_url, $settings->api_key);
             $results = $libre->translateBatch($texts, $from, $to);
        }

        if (!empty($results)) {
            $settings->incrementUsage($totalChars);
        }

        return $results;
    }

    public function getAvailableProviders(?int $tenantId): array
    {
        return TranslationSetting::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->pluck('provider')
            ->toArray();
    }

    public function getActiveProvider(?int $tenantId): ?string
    {
        // Logic to determine preferred provider, maybe 'deepl' if available, else 'libre'
        $providers = $this->getAvailableProviders($tenantId);
        if (in_array('deepl', $providers)) return 'deepl';
        if (in_array('libretranslate', $providers)) return 'libretranslate';
        return null;
    }

    protected function getSettings(?int $tenantId, string $provider): ?TranslationSetting
    {
        return TranslationSetting::where('tenant_id', $tenantId)
            ->where('provider', $provider)
            ->first();
    }
}
