<?php

namespace Modules\Gallery\Filament\Resources;

use Modules\Gallery\Filament\Resources\SlideshowResource\Pages;
use Modules\Gallery\Entities\Slideshow;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlideshowResource extends Resource
{
    protected static ?string $model = Slideshow::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-play-circle';
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
                Forms\Components\Section::make('General Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('album_id')
                            ->relationship('album', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Select an Album as source'),

                        Forms\Components\Select::make('collection_id')
                            ->relationship('collection', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Or select a Collection as source'),
                    ]),
                
                Forms\Components\Section::make('Playback Settings')
                    ->schema([
                        Forms\Components\TextInput::make('duration_per_slide')
                            ->label('Duration per Slide (seconds)')
                            ->numeric()
                            ->default(5)
                            ->required(),
                        
                        Forms\Components\Select::make('transition_type')
                            ->options([
                                'fade' => 'Fade',
                                'slide' => 'Slide',
                                'zoom' => 'Zoom',
                                'ken_burns' => 'Ken Burns',
                                'none' => 'None',
                            ])
                            ->default('fade')
                            ->required(),

                        Forms\Components\FileUpload::make('background_music_path')
                            ->label('Background Music')
                            ->disk('public')
                            ->directory('slideshow-music')
                            ->acceptedFileTypes(['audio/*']),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('auto_play')
                                    ->default(true),
                                Forms\Components\Toggle::make('loop')
                                    ->default(true),
                                Forms\Components\Toggle::make('show_captions')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('album.name')
                    ->label('Album')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collection.name')
                    ->label('Collection')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transition_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSlideshows::route('/'),
            'create' => Pages\CreateSlideshow::route('/create'),
            'edit' => Pages\EditSlideshow::route('/{record}/edit'),
        ];
    }
}
