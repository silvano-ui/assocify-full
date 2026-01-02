<?php

namespace Modules\Localization\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Services\AutoTranslateService;
use Filament\Notifications\Notification;

class BulkTranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $sourceLocale;
    protected array $targetLocales;
    protected array $groups;
    protected string $provider;
    protected ?int $userId;

    public function __construct(string $sourceLocale, array $targetLocales, array $groups, string $provider, ?int $userId = null)
    {
        $this->sourceLocale = $sourceLocale;
        $this->targetLocales = $targetLocales;
        $this->groups = $groups;
        $this->provider = $provider;
        $this->userId = $userId;
    }

    public function handle(AutoTranslateService $service): void
    {
        $query = Translation::where('locale', $this->sourceLocale);
        if (!empty($this->groups)) {
            $query->whereIn('group', $this->groups);
        }
        
        // Chunking to handle memory, though translation is one-by-one or batched by provider usually.
        // DeepL supports array, but AutoTranslateService::translate might be single string.
        // Let's iterate.
        
        $count = 0;

        $query->chunk(100, function ($translations) use ($service, &$count) {
            foreach ($translations as $source) {
                foreach ($this->targetLocales as $targetLocale) {
                    // Check existence
                    $exists = Translation::where('locale', $targetLocale)
                        ->where('group', $source->group)
                        ->where('key', $source->key)
                        ->exists();

                    if (!$exists) {
                        $translatedText = $service->translate($source->value, $targetLocale, $this->sourceLocale, $this->provider);
                        
                        if ($translatedText) {
                            Translation::create([
                                'locale' => $targetLocale,
                                'group' => $source->group,
                                'key' => $source->key,
                                'value' => $translatedText,
                                'is_auto_translated' => true,
                                'auto_translation_provider' => $this->provider,
                            ]);
                            $count++;
                        }
                    }
                }
            }
        });

        if ($this->userId) {
            Notification::make()
                ->title('Bulk Translation Completed')
                ->body("Translated {$count} keys.")
                ->success()
                ->sendToDatabase(\App\Core\Users\User::find($this->userId));
        }
    }
}
