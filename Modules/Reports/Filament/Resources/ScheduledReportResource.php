<?php

namespace Modules\Reports\Filament\Resources;

use Modules\Reports\Filament\Resources\ScheduledReportResource\Pages;
use Modules\Reports\Entities\ScheduledReport;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Modules\Reports\Jobs\ProcessScheduledReportJob;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ScheduledReportResource extends Resource
{
    protected static ?string $model = ScheduledReport::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clock';
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
                    ->required(),
                Select::make('report_template_id')
                    ->relationship('template', 'name')
                    ->required(),
                Select::make('frequency')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ])
                    ->required(),
                TimePicker::make('time'),
                TagsInput::make('recipients')
                    ->placeholder('Add email recipients'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('frequency')
                    ->badge(),
                TextColumn::make('next_run_at')
                    ->dateTime(),
                ToggleColumn::make('is_active'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('run_now')
                    ->action(function (ScheduledReport $record) {
                        ProcessScheduledReportJob::dispatch($record);
                        Notification::make()
                            ->title('Report generation started')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ListScheduledReports::route('/'),
            'create' => Pages\CreateScheduledReport::route('/create'),
            'edit' => Pages\EditScheduledReport::route('/{record}/edit'),
        ];
    }
}
