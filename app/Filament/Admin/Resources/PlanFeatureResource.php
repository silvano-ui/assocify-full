<?php

namespace App\Filament\Admin\Resources;

use App\Core\Features\Feature;
use App\Core\Features\PlanFeature;
use App\Core\Plans\Plan;
use App\Filament\Admin\Resources\PlanFeatureResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class PlanFeatureResource extends Resource
{
    protected static ?string $model = PlanFeature::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Plan Features';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                
                Select::make('feature_slug')
                    ->label('Feature')
                    ->options(Feature::pluck('name', 'slug'))
                    ->required()
                    ->searchable(),

                Toggle::make('included')
                    ->default(true),

                Toggle::make('soft_limit')
                    ->label('Soft Limit (Warning only)')
                    ->default(false),

                TextInput::make('limit_value')
                    ->label('Limit Value')
                    ->numeric()
                    ->helperText('Leave empty for unlimited (if supported) or 0'),
                
                Select::make('limit_type')
                    ->options([
                        'unlimited' => 'Unlimited',
                        'count' => 'Count',
                        'storage_mb' => 'Storage (MB)',
                        'bandwidth_mb' => 'Bandwidth (MB)',
                    ]),

                Select::make('reset_period')
                    ->options([
                        'never' => 'Never',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan.name')->sortable()->searchable(),
                TextColumn::make('feature_slug')->label('Feature')->sortable()->searchable(),
                IconColumn::make('included')->boolean(),
                TextColumn::make('limit_value')->label('Limit'),
                TextColumn::make('limit_type'),
                TextColumn::make('reset_period'),
            ])
            ->filters([
                SelectFilter::make('plan')
                    ->relationship('plan', 'name'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPlanFeatures::route('/'),
            'create' => Pages\CreatePlanFeature::route('/create'),
            'edit' => Pages\EditPlanFeature::route('/{record}/edit'),
        ];
    }
}
