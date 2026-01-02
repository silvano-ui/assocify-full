<?php

namespace Modules\Reports\Filament\Admin\Resources\AdminScheduledReportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Reports\Filament\Admin\Resources\AdminScheduledReportResource;

class ListScheduledReports extends ListRecords
{
    protected static string $resource = AdminScheduledReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
