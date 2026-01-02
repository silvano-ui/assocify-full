<?php

namespace Modules\Reports\Filament\Resources\GeneratedReportResource\Pages;

use Modules\Reports\Filament\Resources\GeneratedReportResource;
use Filament\Resources\Pages\ListRecords;

class ListGeneratedReports extends ListRecords
{
    protected static string $resource = GeneratedReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
