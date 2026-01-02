<?php

namespace Modules\Reports\Filament\Admin\Resources\AdminScheduledReportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Reports\Filament\Admin\Resources\AdminScheduledReportResource;

class EditScheduledReport extends EditRecord
{
    protected static string $resource = AdminScheduledReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
