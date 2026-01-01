<?php

namespace App\Filament\Admin\Resources;

use App\Core\Features\FeatureUsageLog;
use App\Filament\Admin\Resources\FeatureUsageLogResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FeatureUsageLogResource extends Resource
{
    protected static ?string $model = FeatureUsageLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | \UnitEnum | null $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'Usage Logs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]); // Readonly
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('tenant.name')->sortable()->searchable(),
                TextColumn::make('feature_slug')->label('Feature')->sortable()->searchable(),
                TextColumn::make('user.name')->label('User')->sortable(),
                TextColumn::make('action')->sortable(),
                TextColumn::make('quantity'),
                TextColumn::make('result')->badge()->color(fn (string $state): string => match ($state) {
                    'allowed' => 'success',
                    'denied' => 'danger',
                    'soft_warning' => 'warning',
                    default => 'gray',
                }),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->relationship('tenant', 'name'),
                SelectFilter::make('result')
                    ->options([
                        'allowed' => 'Allowed',
                        'denied' => 'Denied',
                        'soft_warning' => 'Soft Warning',
                    ]),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                \Filament\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\FeatureUsageLogExporter::class),
            ])
            ->actions([
                // Read only
            ])
            ->bulkActions([
                // Read only
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
            'index' => Pages\ListFeatureUsageLogs::route('/'),
        ];
    }
}
