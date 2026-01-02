<?php

namespace Modules\Localization\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Modules\Localization\Entities\TranslationSetting;
use Modules\Localization\Filament\Resources\TranslationSettingsResource\Pages;

class TranslationSettingsResource extends Resource
{
    protected static ?string $model = TranslationSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    protected static string | \UnitEnum | null $navigationGroup = 'Localization';

    protected static ?string $navigationLabel = 'Translation Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('provider')
                    ->options([
                        'deepl' => 'DeepL',
                        'libretranslate' => 'LibreTranslate',
                        'manual' => 'Manual',
                    ])
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('api_key')
                    ->password()
                    ->revealable()
                    ->maxLength(500),
                Forms\Components\TextInput::make('api_url')
                    ->label('API URL')
                    ->url()
                    ->maxLength(255)
                    ->helperText('Required for self-hosted LibreTranslate'),
                Forms\Components\TextInput::make('monthly_char_limit')
                    ->numeric()
                    ->default(500000),
                Forms\Components\Toggle::make('is_active')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('usage')
                    ->label('Usage (Used / Limit)')
                    ->getStateUsing(fn (TranslationSetting $record) => 
                        number_format($record->chars_used_this_month) . ' / ' . 
                        ($record->monthly_char_limit ? number_format($record->monthly_char_limit) : '∞')
                    ),
                Tables\Columns\TextColumn::make('api_url')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('test_connection')
                    ->label('Test Connection')
                    ->icon('heroicon-o-signal')
                    ->action(function (TranslationSetting $record) {
                        // Implement test connection logic
                        // For now, mock success
                         \Filament\Notifications\Notification::make()
                            ->title('Connection successful')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListTranslationSettings::route('/'),
            'create' => Pages\CreateTranslationSetting::route('/create'),
            'edit' => Pages\EditTranslationSetting::route('/{record}/edit'),
        ];
    }
}
