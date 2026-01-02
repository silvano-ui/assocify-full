<?php

namespace Modules\Localization\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\TranslationSetting;
use Modules\Localization\Services\AutoTranslateService;

class BulkTranslatePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-language';

    protected string $view = 'localization::filament.pages.bulk-translate-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Localization';

    protected static ?string $title = 'Bulk Translate';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('source_locale')
                    ->label('Source Language')
                    ->options(Language::pluck('name', 'code'))
                    ->required()
                    ->default(config('app.fallback_locale')),
                Select::make('target_locales')
                    ->label('Target Languages')
                    ->options(Language::pluck('name', 'code'))
                    ->multiple()
                    ->required(),
                Select::make('groups')
                    ->label('Groups')
                    ->options(Translation::distinct()->pluck('group', 'group'))
                    ->multiple()
                    ->placeholder('All Groups'),
                Select::make('provider')
                    ->label('Provider')
                    ->options(TranslationSetting::where('is_active', true)->pluck('provider', 'provider'))
                    ->required(),
            ])
            ->statePath('data');
    }

    public function translate(): void
    {
        $data = $this->form->getState();
        
        $sourceLocale = $data['source_locale'];
        $targetLocales = $data['target_locales'];
        $groups = $data['groups'] ?? [];
        $provider = $data['provider'];

        // Logic to count characters and perform translation
        // This is a simplified version. In a real scenario, this should be a Job.
        
        $query = Translation::where('locale', $sourceLocale);
        if (!empty($groups)) {
            $query->whereIn('group', $groups);
        }
        $sourceTranslations = $query->get();

        $count = 0;
        $service = app(AutoTranslateService::class);

        foreach ($targetLocales as $targetLocale) {
            foreach ($sourceTranslations as $source) {
                // Check if translation already exists
                $exists = Translation::where('locale', $targetLocale)
                    ->where('group', $source->group)
                    ->where('key', $source->key)
                    ->exists();
                
                if (!$exists) {
                    $translatedText = $service->translate($source->value, $targetLocale, $sourceLocale, $provider);
                    if ($translatedText) {
                        Translation::create([
                            'locale' => $targetLocale,
                            'group' => $source->group,
                            'key' => $source->key,
                            'value' => $translatedText,
                            'is_auto_translated' => true,
                            'auto_translation_provider' => $provider,
                        ]);
                        $count++;
                    }
                }
            }
        }

        Notification::make()
            ->title('Bulk translation completed')
            ->body("Translated {$count} keys.")
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('translate')
                ->label('Start Translation')
                ->submit('translate'),
        ];
    }
}
