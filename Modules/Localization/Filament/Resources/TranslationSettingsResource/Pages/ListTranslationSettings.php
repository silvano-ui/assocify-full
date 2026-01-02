<?php

namespace Modules\Localization\Filament\Resources\TranslationSettingsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Localization\Filament\Resources\TranslationSettingsResource;

class ListTranslationSettings extends ListRecords
{
    protected static string $resource = TranslationSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
