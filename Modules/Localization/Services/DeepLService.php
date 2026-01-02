<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\TranslationSetting;
use Exception;

class DeepLService
{
    protected $apiKey;
    protected $apiUrl = 'https://api-free.deepl.com/v2'; // Or pro

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function translate(string $text, string $from, string $to): ?string
    {
        if (!$this->apiKey) return null;
        $translatorClass = '\DeepL\Translator';
        if (!class_exists($translatorClass)) return null;

        // Use deeplcom/deepl-php library logic here
        // For simplicity, using curl implementation as placeholder or library wrapper
        
        try {
            $translator = new $translatorClass($this->apiKey);
            $result = $translator->translateText($text, $from, $to);
            return $result->text;
        } catch (Exception $e) {
            // Log error
            return null;
        }
    }

    public function translateBatch(array $texts, string $from, string $to): array
    {
        if (!$this->apiKey) return [];
        $translatorClass = '\DeepL\Translator';
        if (!class_exists($translatorClass)) return [];

        try {
            $translator = new $translatorClass($this->apiKey);
            $results = $translator->translateText($texts, $from, $to);
            
            return array_map(fn($r) => $r->text, $results);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUsage(): array
    {
         if (!$this->apiKey) return ['character_count' => 0, 'character_limit' => 0];
         $translatorClass = '\DeepL\Translator';
         if (!class_exists($translatorClass)) return ['character_count' => 0, 'character_limit' => 0];

         try {
            $translator = new $translatorClass($this->apiKey);
            $usage = $translator->getUsage();
            return [
                'character_count' => $usage->character->count,
                'character_limit' => $usage->character->limit,
            ];
         } catch (Exception $e) {
             return ['character_count' => 0, 'character_limit' => 0];
         }
    }

    public function getSupportedLanguages(): array
    {
         if (!$this->apiKey) return [];
         $translatorClass = '\DeepL\Translator';
         if (!class_exists($translatorClass)) return [];

         try {
            $translator = new $translatorClass($this->apiKey);
            return $translator->getTargetLanguages();
         } catch (Exception $e) {
             return [];
         }
    }
}
