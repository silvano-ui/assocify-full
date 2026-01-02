<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Modules\Localization\Entities\TranslationSetting;

class ResetUsageCommand extends Command
{
    protected $signature = 'localization:reset-usage';
    protected $description = 'Reset monthly character usage for translation providers';

    public function handle()
    {
        $this->info('Resetting monthly usage...');
        
        TranslationSetting::query()->update(['chars_used_this_month' => 0]);

        $this->info('Monthly usage reset successfully.');
        return 0;
    }
}
