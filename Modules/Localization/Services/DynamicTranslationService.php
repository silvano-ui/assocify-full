<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\DynamicTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DynamicTranslationService
{
    protected $autoTranslateService;

    public function __construct(AutoTranslateService $autoTranslateService)
    {
        $this->autoTranslateService = $autoTranslateService;
    }

    public function getTranslation(Model $model, string $field, string $locale): ?string
    {
        $translation = DynamicTranslation::forModel(get_class($model), $model->getKey())
            ->forField($field)
            ->forLocale($locale)
            ->first();

        return $translation ? $translation->value : null;
    }

    public function setTranslation(Model $model, string $field, string $locale, ?string $value): DynamicTranslation
    {
        return DynamicTranslation::updateOrCreate(
            [
                'translatable_type' => get_class($model),
                'translatable_id' => $model->getKey(),
                'field' => $field,
                'locale' => $locale,
            ],
            [
                'value' => $value,
                'is_auto_translated' => false
            ]
        );
    }

    public function autoTranslate(Model $model, string $field, string $fromLocale, array $toLocales): void
    {
        // Get source value
        $sourceValue = $model->{$field}; // Assuming the model has the field value in source locale or stored directly
        // Or get from translation table if source is not base
        
        if (!$sourceValue) return;

        $tenantId = $model->tenant_id ?? null; // Assuming model has tenant_id
        $provider = $this->autoTranslateService->getActiveProvider($tenantId);

        if (!$provider) return;

        foreach ($toLocales as $toLocale) {
            $translatedText = $this->autoTranslateService->translate($sourceValue, $fromLocale, $toLocale, $provider, $tenantId);

            if ($translatedText) {
                DynamicTranslation::updateOrCreate(
                    [
                        'translatable_type' => get_class($model),
                        'translatable_id' => $model->getKey(),
                        'field' => $field,
                        'locale' => $toLocale,
                    ],
                    [
                        'value' => $translatedText,
                        'is_auto_translated' => true,
                        'auto_translation_provider' => $provider
                    ]
                );
            }
        }
    }

    public function hasTranslation(Model $model, string $field, string $locale): bool
    {
        return DynamicTranslation::forModel(get_class($model), $model->getKey())
            ->forField($field)
            ->forLocale($locale)
            ->exists();
    }

    public function getMissingTranslations(Model $model): array
    {
        // Logic to find which locales are missing for this model's translatable fields
        // This requires knowing available locales and translatable fields
        return [];
    }
}
