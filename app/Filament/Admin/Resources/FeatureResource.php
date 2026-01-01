<?php

namespace App\Filament\Admin\Resources;

use App\Core\Features\Feature;
use App\Filament\Admin\Resources\FeatureResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required(),
                TextInput::make('module')
                    ->required(),
                TextInput::make('category'),
                TextInput::make('description')
                    ->columnSpanFull(),
                TextInput::make('icon'),
                TextInput::make('unit_name')
                    ->label('Unit Name (e.g., "users", "GB")'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->default(true),
                Toggle::make('is_premium')
                    ->default(false),
                Toggle::make('is_beta')
                    ->default(false),
                
                TextInput::make('price_monthly')
                    ->numeric()
                    ->prefix('€'),
                TextInput::make('price_yearly')
                    ->numeric()
                    ->prefix('€'),
                TextInput::make('price_per_unit')
                    ->numeric()
                    ->prefix('€'),

                KeyValue::make('settings')
                    ->label('Settings JSON'),
                
                Repeater::make('dependencies')
                    ->relationship('dependencies')
                    ->schema([
                        Select::make('requires_feature_slug')
                            ->label('Requires Feature')
                            ->options(Feature::pluck('name', 'slug'))
                            ->required()
                            ->searchable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')->searchable()->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('module')->sortable(),
                TextColumn::make('category')->sortable(),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('is_premium')->boolean(),
                IconColumn::make('is_beta')->boolean(),
                TextColumn::make('price_monthly')->money('eur'),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->options(fn() => Feature::distinct()->pluck('module', 'module')->toArray()),
                SelectFilter::make('category')
                    ->options(fn() => Feature::distinct()->whereNotNull('category')->pluck('category', 'category')->toArray()),
                TernaryFilter::make('is_premium'),
                TernaryFilter::make('is_beta'),
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
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
