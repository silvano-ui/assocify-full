<?php

namespace Modules\Reports\Filament\Admin\Resources;

use Modules\Reports\Filament\Resources\GeneratedReportResource;
use Modules\Reports\Filament\Admin\Resources\AdminGeneratedReportResource\Pages;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminGeneratedReportResource extends GeneratedReportResource
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
            'index' => Pages\ListGeneratedReports::route('/'),
        ];
    }
}
