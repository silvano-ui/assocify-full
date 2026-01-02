<?php

namespace Modules\Localization\Filament\Resources\TranslationSettingsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Localization\Filament\Resources\TranslationSettingsResource;

class EditTranslationSetting extends EditRecord
{
    protected static string $resource = TranslationSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
