<?php

namespace Modules\Reports\Filament\Admin\Resources\AdminReportTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Reports\Filament\Admin\Resources\AdminReportTemplateResource;

class EditReportTemplate extends EditRecord
{
    protected static string $resource = AdminReportTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
