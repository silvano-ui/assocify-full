<?php

namespace Modules\Localization\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\TenantLanguage;

class LanguageSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';

    protected string $view = 'localization::filament.pages.language-settings-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Language Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = filament()->getTenant();
        if (!$tenant) {
            return;
        }

        // Get enabled languages
        $enabledLanguageIds = TenantLanguage::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->pluck('language_id')
            ->toArray();

        $this->form->fill([
            'default_locale' => $tenant->default_locale ?? config('app.locale'),
            'enabled_languages' => $enabledLanguageIds,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('default_locale')
                    ->label('Default Language')
                    ->options(Language::where('is_active', true)->pluck('name', 'code'))
                    ->required(),
                CheckboxList::make('enabled_languages')
                    ->label('Enabled Languages')
                    ->options(Language::where('is_active', true)->pluck('name', 'id'))
                    ->columns(3)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $tenant = filament()->getTenant();

        // Update default locale
        $tenant->update(['default_locale' => $data['default_locale']]);

        // Update enabled languages
        // We sync tenant_languages.
        // Get existing
        $existing = TenantLanguage::where('tenant_id', $tenant->id)->pluck('language_id')->toArray();
        $new = $data['enabled_languages'];

        // To add
        $toAdd = array_diff($new, $existing);
        foreach ($toAdd as $langId) {
            TenantLanguage::create([
                'tenant_id' => $tenant->id,
                'language_id' => $langId,
                'is_active' => true,
            ]);
        }

        // To remove (disable)
        $toRemove = array_diff($existing, $new);
        TenantLanguage::where('tenant_id', $tenant->id)
            ->whereIn('language_id', $toRemove)
            ->update(['is_active' => false]);
            
        // Also re-enable if it was disabled
        TenantLanguage::where('tenant_id', $tenant->id)
            ->whereIn('language_id', $toAdd)
            ->update(['is_active' => true]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
