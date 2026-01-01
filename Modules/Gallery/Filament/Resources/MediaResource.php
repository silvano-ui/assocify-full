<?php

namespace Modules\Gallery\Filament\Resources;

use Modules\Gallery\Filament\Resources\MediaResource\Pages;
use Modules\Gallery\Entities\Media;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

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
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Media File')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('File')
                                    ->disk('public')
                                    ->directory('gallery')
                                    ->image()
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'image' => 'Image',
                                        'video' => 'Video',
                                        'audio' => 'Audio',
                                        'panorama_360' => '360 Panorama',
                                        'drone' => 'Drone Shot',
                                    ])
                                    ->required(),
                            ]),
                        Forms\Components\Section::make('Details')
                            ->schema([
                                Forms\Components\Select::make('album_id')
                                    ->relationship('album', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('caption')
                                    ->maxLength(255),
                                Forms\Components\Select::make('tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\ColorPicker::make('color'),
                                    ]),
                            ]),
                    ])->columnSpan(2),
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Metadata')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '-'),
                                Forms\Components\Placeholder::make('updated_at')
                                    ->content(fn ($record) => $record?->updated_at?->diffForHumans() ?? '-'),
                                Forms\Components\Placeholder::make('size')
                                    ->content(fn ($record) => $record ? number_format($record->size / 1024, 2) . ' KB' : '-'),
                                Forms\Components\Placeholder::make('mime')
                                    ->content(fn ($record) => $record?->mime ?? '-'),
                            ]),
                        Forms\Components\Section::make('Statistics')
                            ->schema([
                                Forms\Components\Placeholder::make('views_count')
                                    ->label('Views')
                                    ->content(fn ($record) => $record?->views_count ?? 0),
                                Forms\Components\Placeholder::make('downloads_count')
                                    ->label('Downloads')
                                    ->content(fn ($record) => $record?->downloads_count ?? 0),
                                Forms\Components\Placeholder::make('likes_count')
                                    ->label('Likes')
                                    ->content(fn ($record) => $record?->likes_count ?? 0),
                            ]),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('original_name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('album.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                        'audio' => 'Audio',
                        'panorama_360' => '360 Panorama',
                        'drone' => 'Drone Shot',
                    ]),
                Tables\Filters\SelectFilter::make('album')
                    ->relationship('album', 'name'),
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
