<?php

namespace App\Filament\Exports;

use App\Core\Features\FeatureUsageLog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class FeatureUsageLogExporter extends Exporter
{
    protected static ?string $model = FeatureUsageLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at'),
            ExportColumn::make('tenant.name'),
            ExportColumn::make('feature_slug'),
            ExportColumn::make('user.name'),
            ExportColumn::make('action'),
            ExportColumn::make('quantity'),
            ExportColumn::make('result'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your feature usage log export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
