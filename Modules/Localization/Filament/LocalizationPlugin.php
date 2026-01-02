<?php

namespace Modules\Localization\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Localization\Filament\Pages\BulkTranslatePage;
use Modules\Localization\Filament\Pages\DynamicTranslationsPage;
use Modules\Localization\Filament\Pages\LanguageSettingsPage;
use Modules\Localization\Filament\Pages\TranslationImportPage;
use Modules\Localization\Filament\Resources\LanguageResource;
use Modules\Localization\Filament\Resources\TenantTranslationResource;
use Modules\Localization\Filament\Resources\TranslationResource;
use Modules\Localization\Filament\Resources\TranslationSettingsResource;
use Modules\Localization\Filament\Widgets\ProviderUsageWidget;
use Modules\Localization\Filament\Widgets\TranslationCoverageWidget;
use Modules\Localization\Filament\Widgets\TranslationStatsWidget;

class LocalizationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'localization';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                LanguageResource::class,
                TranslationResource::class,
                TranslationSettingsResource::class,
                TenantTranslationResource::class,
            ])
            ->pages([
                BulkTranslatePage::class,
                TranslationImportPage::class,
                LanguageSettingsPage::class,
                DynamicTranslationsPage::class,
            ])
            ->widgets([
                TranslationStatsWidget::class,
                TranslationCoverageWidget::class,
                ProviderUsageWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }
}
