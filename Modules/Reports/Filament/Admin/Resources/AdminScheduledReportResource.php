<?php

namespace Modules\Reports\Filament\Admin\Resources;

use Modules\Reports\Filament\Resources\ScheduledReportResource;
use Modules\Reports\Filament\Admin\Resources\AdminScheduledReportResource\Pages;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminScheduledReportResource extends ScheduledReportResource
{
    public static function getNavigationGroup(): ?string
    {
        return 'Reports & Analytics';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope('tenant');
    }

    public static function table(Table $table): Table
    {
        $table = parent::table($table);
        
        return $table->filters([
            SelectFilter::make('tenant')
                ->relationship('tenant', 'name')
                ->searchable()
                ->preload(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduledReports::route('/'),
            'create' => Pages\CreateScheduledReport::route('/create'),
            'edit' => Pages\EditScheduledReport::route('/{record}/edit'),
        ];
    }
}
