<?php

namespace Modules\Localization\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Localization\Services\TranslationService;

class SyncTranslationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(TranslationService $service): void
    {
        // Scan lang files and sync to DB
        // This logic might be heavy, so it's a job.
        
        // We can call a method on TranslationService if it exists, or implement here.
        // Let's assume we implement basic syncing here or delegate.
        
        // 1. Get all locales from files
        $langPath = lang_path();
        $files = \Illuminate\Support\Facades\File::allFiles($langPath);
        
        foreach ($files as $file) {
            $locale = $file->getRelativePath(); // e.g. "en" or "it"
            $group = $file->getFilenameWithoutExtension(); // e.g. "auth"
            
            if (empty($locale)) {
                // Should be inside a folder
                continue;
            }

            $lines = \Illuminate\Support\Facades\File::getRequire($file->getPathname());
            
            if (!is_array($lines)) {
                continue;
            }
            
            $service->importTranslations($locale, $lines, $group);
        }
    }
}
