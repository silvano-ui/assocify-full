<?php

namespace Modules\Localization\Traits;

use Modules\Localization\Entities\DynamicTranslation;

trait Translatable
{
    /**
     * Get all translations for this model.
     */
    public function translations()
    {
        return $this->morphMany(DynamicTranslation::class, 'translatable');
    }

    /**
     * Get translation for a specific field and locale.
     */
    public function getTranslation(string $field, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        // Check if we have a translation in DB
        // Optimization: Eager load translations if not already loaded?
        // Or access relation if loaded.
        
        $translation = $this->translations
            ->where('field', $field)
            ->where('locale', $locale)
            ->first();

        if ($translation) {
            return $translation->value;
        }

        // Fallback logic
        // If requesting fallback locale, maybe return the attribute itself if it holds the default value
        $fallback = config('app.fallback_locale');
        if ($locale === $fallback) {
            return $this->getAttribute($field);
        }

        return null; // Or return default value?
    }

    /**
     * Set translation for a specific field and locale.
     */
    public function setTranslation(string $field, string $locale, $value)
    {
        $this->translations()->updateOrCreate(
            [
                'field' => $field,
                'locale' => $locale,
            ],
            [
                'value' => $value,
                'is_auto_translated' => false, // Manual set implies not auto, or handled by caller
            ]
        );
        
        return $this;
    }

    /**
     * Check if translation exists.
     */
    public function hasTranslation(string $field, ?string $locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations
            ->where('field', $field)
            ->where('locale', $locale)
            ->isNotEmpty();
    }
}
