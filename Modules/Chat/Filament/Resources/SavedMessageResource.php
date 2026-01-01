<?php

namespace Modules\Chat\Filament\Resources;

use Modules\Chat\Filament\Resources\SavedMessageResource\Pages;
use Modules\Chat\Entities\SavedMessage;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SavedMessageResource extends Resource
{
    protected static ?string $model = SavedMessage::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bookmark';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Chat';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('message_id')
                    ->relationship('message', 'id')
                    ->required(),
                Forms\Components\Textarea::make('note'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('message.body')->limit(30),
                Tables\Columns\TextColumn::make('note')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSavedMessages::route('/'),
            'create' => Pages\CreateSavedMessage::route('/create'),
            'edit' => Pages\EditSavedMessage::route('/{record}/edit'),
        ];
    }
}
