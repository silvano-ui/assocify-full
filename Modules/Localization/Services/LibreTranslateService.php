<?php

namespace Modules\Localization\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class LibreTranslateService
{
    protected $baseUrl = 'https://libretranslate.com'; // Default public
    protected $apiKey;

    public function __construct(?string $url = null, ?string $apiKey = null)
    {
        if ($url) $this->baseUrl = $url;
        $this->apiKey = $apiKey;
    }

    public function translate(string $text, string $from, string $to): ?string
    {
        try {
            $response = Http::post("{$this->baseUrl}/translate", [
                'q' => $text,
                'source' => $from,
                'target' => $to,
                'format' => 'text',
                'api_key' => $this->apiKey
            ]);

            if ($response->successful()) {
                return $response->json('translatedText');
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function translateBatch(array $texts, string $from, string $to): array
    {
        // LibreTranslate might not support batch in same way, loop for now
        $results = [];
        foreach ($texts as $text) {
            $results[] = $this->translate($text, $from, $to);
        }
        return $results;
    }

    public function getSupportedLanguages(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/languages");
            if ($response->successful()) {
                return $response->json();
            }
            return [];
        } catch (Exception $e) {
            return [];
        }
    }
}
