<?php

namespace Modules\Gallery\Filament\Resources;

use Modules\Gallery\Filament\Resources\AlbumResource\Pages;
use Modules\Gallery\Entities\Album;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gallery';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description'),
                Forms\Components\Select::make('visibility')
                    ->options([
                        'public' => 'Public',
                        'members' => 'Members',
                        'participants' => 'Participants',
                        'private' => 'Private',
                        'link_only' => 'Link Only',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_collaborative')
                    ->default(false),
                Forms\Components\TextInput::make('password')
                    ->password(),
                Forms\Components\Toggle::make('download_enabled')
                    ->default(true),
                Forms\Components\Toggle::make('allow_comments')
                    ->default(true),
                Forms\Components\Toggle::make('allow_likes')
                    ->default(true),
                Forms\Components\Toggle::make('allow_external_share')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('visibility'),
                Tables\Columns\IconColumn::make('is_collaborative')->boolean(),
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
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
