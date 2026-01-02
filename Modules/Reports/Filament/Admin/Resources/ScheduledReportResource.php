<?php

namespace Modules\Reports\Filament\Admin\Resources;

use Modules\Reports\Filament\Admin\Resources\ScheduledReportResource\Pages;
use Modules\Reports\Entities\ScheduledReport;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ScheduledReportResource extends Resource
{
    protected static ?string $model = ScheduledReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string | \UnitEnum | null $navigationGroup = 'Reports & Analytics';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope('tenant');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('Tenant'),
                TextColumn::make('name'),
                TextColumn::make('frequency'),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->relationship('tenant', 'name'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduledReports::route('/'),
        ];
    }
}
