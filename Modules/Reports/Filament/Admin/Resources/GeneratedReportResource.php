<?php

namespace Modules\Reports\Filament\Admin\Resources;

use Modules\Reports\Filament\Admin\Resources\GeneratedReportResource\Pages;
use Modules\Reports\Entities\GeneratedReport;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class GeneratedReportResource extends Resource
{
    protected static ?string $model = GeneratedReport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

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
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->relationship('tenant', 'name'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneratedReports::route('/'),
        ];
    }
}
