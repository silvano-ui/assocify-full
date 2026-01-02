<?php

namespace Modules\Localization\Filament\Resources\TranslationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Localization\Filament\Pages\BulkTranslatePage;
use Modules\Localization\Filament\Pages\TranslationImportPage;
use Modules\Localization\Filament\Resources\TranslationResource;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(TranslationImportPage::getUrl()),
            Actions\Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Implement export logic here, maybe download a JSON/CSV
                    // For now, just a placeholder notification
                    \Filament\Notifications\Notification::make()
                        ->title('Export started')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('bulk_translate')
                ->label('Bulk Translate')
                ->icon('heroicon-o-language')
                ->url(BulkTranslatePage::getUrl()),
        ];
    }
}
