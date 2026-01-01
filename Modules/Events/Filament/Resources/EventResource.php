<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\EventResource\Pages;
use Modules\Events\Entities\Event;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Events';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Event::class, 'slug', ignoreRecord: true),
                                Forms\Components\Select::make('event_category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Section::make('Date & Time')
                            ->schema([
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('ends_at'),
                                Forms\Components\DateTimePicker::make('registration_starts'),
                                Forms\Components\DateTimePicker::make('registration_ends'),
                            ])->columns(2),
                         Forms\Components\Section::make('Location')
                            ->schema([
                                Forms\Components\TextInput::make('location')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('lat')
                                    ->numeric(),
                                Forms\Components\TextInput::make('lng')
                                    ->numeric(),
                            ])->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'cancelled' => 'Cancelled',
                                        'completed' => 'Completed',
                                    ])
                                    ->required()
                                    ->default('draft'),
                                Forms\Components\Toggle::make('is_public')
                                    ->required()
                                    ->default(true),
                                Forms\Components\Toggle::make('requires_approval')
                                    ->required()
                                    ->default(false),
                            ]),
                        Forms\Components\Section::make('Pricing')
                            ->schema([
                                Forms\Components\Toggle::make('is_free')
                                    ->live()
                                    ->default(true),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('€')
                                    ->hidden(fn (Forms\Get $get) => $get('is_free')),
                                Forms\Components\TextInput::make('max_participants')
                                    ->numeric(),
                            ]),
                         Forms\Components\Section::make('Image')
                            ->schema([
                                Forms\Components\FileUpload::make('cover_image')
                                    ->image(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'cancelled',
                        'success' => 'published',
                        'info' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('price')
                    ->money('eur')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
