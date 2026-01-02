<?php

namespace Modules\Localization\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Localization\Services\DynamicTranslationService;

class TranslateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $modelType;
    protected int $modelId;
    protected string $field;
    protected string $fromLocale;
    protected array $toLocales;

    public function __construct(string $modelType, int $modelId, string $field, string $fromLocale, array $toLocales)
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->field = $field;
        $this->fromLocale = $fromLocale;
        $this->toLocales = $toLocales;
    }

    public function handle(DynamicTranslationService $service): void
    {
        // Fetch source content
        // Assuming model exists and has the field
        // We might need to instantiate the model or just query DB if we know the table.
        // But DynamicTranslationService likely handles retrieving or we pass the value?
        // The job signature says "model_type, model_id".
        
        $modelClass = $this->modelType;
        if (!class_exists($modelClass)) {
            return;
        }
        
        $model = $modelClass::find($this->modelId);
        if (!$model) {
            return;
        }
        
        // Value to translate. 
        // If the model uses Translatable trait, getting the attribute might return the translation for current locale.
        // We want the raw value for $this->fromLocale.
        // If using spatie/laravel-translatable, it's getTranslation().
        // If using our own logic, we assume the model field itself holds the value OR we look up DynamicTranslation for source?
        // Usually, the "source" is the main record's column (if not fully normalized) or a translation record.
        // Let's assume the main column holds the default locale value OR we use a method to get it.
        
        $value = $model->getAttribute($this->field);
        // If value is null, nothing to translate
        if (empty($value)) {
            return;
        }

        foreach ($this->toLocales as $locale) {
             // Use service to auto translate and save
             // We need a method in DynamicTranslationService that accepts raw params
             // Or we use AutoTranslateService directly and then save using DynamicTranslationService.
             
             // Let's use AutoTranslateService to get text, then save.
             $autoTranslate = app(\Modules\Localization\Services\AutoTranslateService::class);
             $translatedText = $autoTranslate->translate($value, $locale, $this->fromLocale);
             
             if ($translatedText) {
                 $service->setTranslation(
                     $model,
                     $this->field,
                     $locale,
                     $translatedText
                 );
             }
        }
    }
}
