<?php

namespace Modules\Members\Filament\Resources;

use Modules\Members\Filament\Resources\MemberCategoryResource\Pages;
use Modules\Members\Entities\MemberCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Core\ModuleManager\ModuleManager;
use Filament\Facades\Filament;

class MemberCategoryResource extends Resource
{
    protected static ?string $model = MemberCategory::class;

    protected static string | \UnitEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Members';

    public static function canViewAny(): bool
    {
        // Check if module is enabled for the current tenant
        $tenant = Filament::getTenant();
        if (!$tenant) {
            return false;
        }
        return app(ModuleManager::class)->isEnabled('members', $tenant->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535),
                Forms\Components\TextInput::make('annual_fee')
                    ->required()
                    ->numeric()
                    ->prefix('€'),
                Forms\Components\TextInput::make('age_min')
                    ->numeric(),
                Forms\Components\TextInput::make('age_max')
                    ->numeric(),
                Forms\Components\Toggle::make('is_default')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Forms\Components\ColorPicker::make('color'),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('annual_fee')
                    ->money('eur'),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListMemberCategories::route('/'),
            'create' => Pages\CreateMemberCategory::route('/create'),
            'edit' => Pages\EditMemberCategory::route('/{record}/edit'),
        ];
    }
}
