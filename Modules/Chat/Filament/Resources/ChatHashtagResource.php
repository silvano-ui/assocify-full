<?php

namespace Modules\Chat\Filament\Resources;

use Modules\Chat\Filament\Resources\ChatHashtagResource\Pages;
use Modules\Chat\Entities\ChatHashtag;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatHashtagResource extends Resource
{
    protected static ?string $model = ChatHashtag::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-hashtag';
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
                Forms\Components\TextInput::make('tag')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('message_count')
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('last_used_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tag')->searchable(),
                Tables\Columns\TextColumn::make('message_count')->sortable(),
                Tables\Columns\TextColumn::make('last_used_at')->dateTime()->sortable(),
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
            'index' => Pages\ListChatHashtags::route('/'),
            'create' => Pages\CreateChatHashtag::route('/create'),
            'edit' => Pages\EditChatHashtag::route('/{record}/edit'),
        ];
    }
}
