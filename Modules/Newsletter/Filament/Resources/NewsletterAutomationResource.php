<?php

namespace Modules\Newsletter\Filament\Resources;

use Modules\Newsletter\Filament\Resources\NewsletterAutomationResource\Pages;
use Modules\Newsletter\Filament\Resources\NewsletterAutomationResource\RelationManagers;
use Modules\Newsletter\Entities\NewsletterAutomation;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class NewsletterAutomationResource extends Resource
{
    protected static ?string $model = NewsletterAutomation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

    protected static string | \UnitEnum | null $navigationGroup = 'Newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('trigger_type')
                    ->options([
                        'subscription' => 'Subscription',
                        'birthday' => 'Birthday',
                        'event_registration' => 'Event Registration',
                        'event_reminder' => 'Event Reminder',
                        'membership_expiry' => 'Membership Expiry',
                        'membership_renewed' => 'Membership Renewed',
                        'welcome' => 'Welcome',
                        'custom' => 'Custom',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('trigger_type')
                    ->badge(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('total_sent')
                    ->label('Sent'),
                TextColumn::make('total_opened')
                    ->label('Opened'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StepsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterAutomations::route('/'),
            'create' => Pages\CreateNewsletterAutomation::route('/create'),
            'edit' => Pages\EditNewsletterAutomation::route('/{record}/edit'),
        ];
    }
}
