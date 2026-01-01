<?php

namespace App\Filament\Admin\Resources;

use App\Core\Features\Feature;
use App\Core\Features\FeatureBundle;
use App\Filament\Admin\Resources\FeatureBundleResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class FeatureBundleResource extends Resource
{
    protected static ?string $model = FeatureBundle::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-gift';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bundle Details')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pricing')
                    ->schema([
                        TextInput::make('price_monthly')
                            ->required()
                            ->numeric()
                            ->prefix('€'),
                        TextInput::make('price_yearly')
                            ->required()
                            ->numeric()
                            ->prefix('€'),
                        TextInput::make('discount_percent')
                            ->numeric()
                            ->suffix('%')
                            ->default(0),
                        Toggle::make('is_active')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Included Features')
                    ->schema([
                        Select::make('features')
                            ->relationship('features', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')->searchable()->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('price_monthly')->money('eur'),
                TextColumn::make('discount_percent')->suffix('%'),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('features_count')->counts('features')->label('Features'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListFeatureBundles::route('/'),
            'create' => Pages\CreateFeatureBundle::route('/create'),
            'edit' => Pages\EditFeatureBundle::route('/{record}/edit'),
        ];
    }
}
