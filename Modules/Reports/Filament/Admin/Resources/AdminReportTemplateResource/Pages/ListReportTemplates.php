<?php

namespace Modules\Reports\Filament\Admin\Resources\AdminReportTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Reports\Filament\Admin\Resources\AdminReportTemplateResource;

class ListReportTemplates extends ListRecords
{
    protected static string $resource = AdminReportTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
