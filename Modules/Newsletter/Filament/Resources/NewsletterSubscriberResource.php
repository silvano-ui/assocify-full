<?php

namespace Modules\Newsletter\Filament\Resources;

use Modules\Newsletter\Filament\Resources\NewsletterSubscriberResource\Pages;
use Modules\Newsletter\Entities\NewsletterSubscriber;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('name'),
                Forms\Components\Select::make('status')
                    ->options([
                        'subscribed' => 'Subscribed',
                        'unsubscribed' => 'Unsubscribed',
                        'bounced' => 'Bounced',
                        'complained' => 'Complained',
                    ])
                    ->required(),
                Forms\Components\Select::make('list_id')
                    ->relationship('list', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'subscribed',
                        'danger' => 'unsubscribed',
                        'warning' => 'bounced',
                        'gray' => 'complained',
                    ]),
                TextColumn::make('list.name')
                    ->label('List'),
                TextColumn::make('source'),
            ])
            ->filters([
                SelectFilter::make('list')
                    ->relationship('list', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'subscribed' => 'Subscribed',
                        'unsubscribed' => 'Unsubscribed',
                        'bounced' => 'Bounced',
                        'complained' => 'Complained',
                    ]),
                SelectFilter::make('source')
                    ->options([
                        'manual' => 'Manual',
                        'import' => 'Import',
                        'registration' => 'Registration',
                        'event' => 'Event',
                        'api' => 'API',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('subscribe')
                    ->label('Subscribe')
                    ->icon('heroicon-o-check')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'subscribed'])),
                BulkAction::make('unsubscribe')
                    ->label('Unsubscribe')
                    ->icon('heroicon-o-x-mark')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'unsubscribed'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
