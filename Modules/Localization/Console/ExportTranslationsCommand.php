<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Modules\Localization\Entities\Translation;

class ExportTranslationsCommand extends Command
{
    protected $signature = 'localization:export {locale} {--format=json}';
    protected $description = 'Export translations to a file';

    public function handle()
    {
        $locale = $this->argument('locale');
        $format = $this->option('format');

        $translations = Translation::where('locale', $locale)->get()->groupBy('group');
        $output = [];

        foreach ($translations as $group => $items) {
            foreach ($items as $item) {
                $output[$group][$item->key] = $item->value;
            }
        }

        if ($format === 'json') {
            echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            // PHP export format
            echo "<?php\n\nreturn " . var_export($output, true) . ";\n";
        }

        return 0;
    }
}
