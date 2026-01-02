<?php

namespace Modules\Localization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\TranslationSetting;

class LocalizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Default Languages
        $languages = [
            ['code' => 'it', 'name' => 'Italiano', 'native_name' => 'Italiano', 'flag' => '🇮🇹', 'is_active' => true, 'is_default' => true],
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧', 'is_active' => true, 'is_default' => false],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'flag' => '🇩🇪', 'is_active' => false, 'is_default' => false],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'flag' => '🇫🇷', 'is_active' => false, 'is_default' => false],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'flag' => '🇪🇸', 'is_active' => false, 'is_default' => false],
            ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português', 'flag' => '🇵🇹', 'is_active' => false, 'is_default' => false],
            ['code' => 'nl', 'name' => 'Dutch', 'native_name' => 'Nederlands', 'flag' => '🇳🇱', 'is_active' => false, 'is_default' => false],
            ['code' => 'pl', 'name' => 'Polish', 'native_name' => 'Polski', 'flag' => '🇵🇱', 'is_active' => false, 'is_default' => false],
            ['code' => 'ro', 'name' => 'Romanian', 'native_name' => 'Română', 'flag' => '🇷🇴', 'is_active' => false, 'is_default' => false],
            ['code' => 'el', 'name' => 'Greek', 'native_name' => 'Ελληνικά', 'flag' => '🇬🇷', 'is_active' => false, 'is_default' => false],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(
                ['code' => $lang['code']],
                $lang
            );
        }

        // 2. Create Translation Settings (Platform level, tenant_id null)
        TranslationSetting::updateOrCreate(
            ['tenant_id' => null, 'provider' => 'deepl'],
            [
                'api_url' => 'https://api-free.deepl.com/v2',
                'is_active' => false,
                'monthly_char_limit' => 500000,
            ]
        );

        TranslationSetting::updateOrCreate(
            ['tenant_id' => null, 'provider' => 'libretranslate'],
            [
                'api_url' => 'https://libretranslate.com',
                'is_active' => true,
                'monthly_char_limit' => null,
            ]
        );

        // 3. Import base Italian translations (Optional, mock)
        // In real app, we might call SyncTranslationsJob or artisan command
        // But let's just log or skip as we don't have the files yet really populated
    }
}
