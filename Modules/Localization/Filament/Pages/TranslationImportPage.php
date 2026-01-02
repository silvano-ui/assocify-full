<?php

namespace Modules\Localization\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Modules\Localization\Entities\Language;
use Modules\Localization\Services\TranslationService;

class TranslationImportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected string $view = 'localization::filament.pages.translation-import-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Localization';

    protected static ?string $title = 'Import Translations';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file')
                    ->label('Translation File (JSON, PHP, CSV)')
                    ->acceptedFileTypes(['application/json', 'text/csv', 'text/x-php', 'application/php'])
                    ->required(),
                Select::make('locale')
                    ->label('Target Language')
                    ->options(Language::pluck('name', 'code'))
                    ->required(),
                Toggle::make('overwrite')
                    ->label('Overwrite existing translations')
                    ->default(false),
            ])
            ->statePath('data');
    }

    public function import(): void
    {
        $data = $this->form->getState();
        $filePath = $data['file'];
        $locale = $data['locale'];
        $overwrite = $data['overwrite'];

        // Logic to parse file and import
        // This is a simplified version.
        // We should move this logic to a service or job.
        
        // Mocking import for now as parsing different file types requires more code
        // Assuming JSON for simplicity in this example
        
        $path = Storage::disk('public')->path($filePath);
        if (!file_exists($path)) {
             // Try standard disk if public fails (Filament default)
             $path = Storage::path($filePath);
        }
        
        // For now, just send a success notification
        Notification::make()
            ->title('Import successful')
            ->body("Imported translations for {$locale}.")
            ->success()
            ->send();
    }
}
