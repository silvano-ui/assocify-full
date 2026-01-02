<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\Language;
use Illuminate\Database\Eloquent\Collection;

class LanguageService
{
    public function getActiveLanguages(): Collection
    {
        return Language::active()->orderBy('sort_order')->get();
    }

    public function getDefaultLanguage(): ?Language
    {
        return Language::default()->first();
    }

    public function getAllLanguages(): Collection
    {
        return Language::orderBy('sort_order')->get();
    }

    public function createLanguage(string $code, string $name, string $nativeName, ?string $flag = null, string $direction = 'ltr'): Language
    {
        return Language::create([
            'code' => $code,
            'name' => $name,
            'native_name' => $nativeName,
            'flag' => $flag,
            'direction' => $direction,
            'is_active' => false,
            'is_default' => false,
        ]);
    }

    public function setDefaultLanguage(string $code): bool
    {
        Language::query()->update(['is_default' => false]);
        return (bool) Language::where('code', $code)->update(['is_default' => true]);
    }

    public function activateLanguage(string $code): bool
    {
        return (bool) Language::where('code', $code)->update(['is_active' => true]);
    }

    public function deactivateLanguage(string $code): bool
    {
        // Prevent deactivating default language
        $language = $this->getLanguageByCode($code);
        if ($language && $language->is_default) {
            return false;
        }

        return (bool) Language::where('code', $code)->update(['is_active' => false]);
    }

    public function getLanguageByCode(string $code): ?Language
    {
        return Language::where('code', $code)->first();
    }
}
