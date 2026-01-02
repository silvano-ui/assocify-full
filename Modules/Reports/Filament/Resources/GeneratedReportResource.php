<?php

namespace Modules\Reports\Filament\Resources;

use Modules\Reports\Filament\Resources\GeneratedReportResource\Pages;
use Modules\Reports\Entities\GeneratedReport;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Storage;

class GeneratedReportResource extends Resource
{
    protected static ?string $model = GeneratedReport::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('template.name')
                    ->label('Report')
                    ->searchable(),
                TextColumn::make('format')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('file_size')
                    ->formatStateUsing(fn ($record) => $record->formatted_file_size),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (GeneratedReport $record) => $record->isCompleted())
                    ->action(fn (GeneratedReport $record) => response()->download(storage_path('app/reports/' . $record->file_path))),
                DeleteAction::make(),
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
            'index' => Pages\ListGeneratedReports::route('/'),
        ];
    }
}
