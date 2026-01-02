<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Modules\Localization\Services\TranslationService;
use Illuminate\Support\Facades\File;

class ImportTranslationsCommand extends Command
{
    protected $signature = 'localization:import {locale} {file}';
    protected $description = 'Import translations from a file';

    public function handle(TranslationService $service)
    {
        $locale = $this->argument('locale');
        $file = $this->argument('file');

        if (!File::exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $extension = File::extension($file);
        $data = [];

        if ($extension === 'php') {
            $data = require $file;
        } elseif ($extension === 'json') {
            $data = json_decode(File::get($file), true);
        } else {
             $this->error("Unsupported file type: {$extension}");
             return 1;
        }

        if (!is_array($data)) {
            $this->error("Invalid file content");
            return 1;
        }
        
        $group = pathinfo($file, PATHINFO_FILENAME);

        $service->importTranslations($locale, $data, $group);

        $this->info("Imported translations for {$locale} from {$file}");
        return 0;
    }
}
