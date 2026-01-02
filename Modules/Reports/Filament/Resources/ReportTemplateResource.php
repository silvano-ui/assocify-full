<?php

namespace Modules\Reports\Filament\Resources;

use Modules\Reports\Filament\Resources\ReportTemplateResource\Pages;
use Modules\Reports\Entities\ReportTemplate;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ReportTemplateResource extends Resource
{
    protected static ?string $model = ReportTemplate::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->maxLength(255),
                Select::make('data_source')
                    ->options(config('reports.data_sources', []))
                    ->required(),
                TextInput::make('icon')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->default(true),
                Toggle::make('allow_export_pdf')
                    ->label('PDF Export'),
                Toggle::make('allow_export_excel')
                    ->label('Excel Export'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('data_source')
                    ->badge(),
                ToggleColumn::make('is_active'),
                TextColumn::make('generated_reports_count')
                    ->counts('generatedReports')
                    ->label('Generated'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportTemplates::route('/'),
            'create' => Pages\CreateReportTemplate::route('/create'),
            'edit' => Pages\EditReportTemplate::route('/{record}/edit'),
        ];
    }
}
