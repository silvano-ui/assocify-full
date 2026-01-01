<?php

namespace Modules\Newsletter\Filament\Resources;

use Modules\Newsletter\Filament\Resources\NewsletterListResource\Pages;
use Modules\Newsletter\Entities\NewsletterList;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class NewsletterListResource extends Resource
{
    protected static ?string $model = NewsletterList::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string | \UnitEnum | null $navigationGroup = 'Newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535),
                Select::make('type')
                    ->options([
                        'manual' => 'Manual',
                        'dynamic' => 'Dynamic',
                        'all_members' => 'All Members',
                    ])
                    ->required()
                    ->reactive(),
                KeyValue::make('dynamic_filters')
                    ->visible(fn (callable $get) => $get('type') === 'dynamic'),
                Toggle::make('is_default'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('subscribers_count')
                    ->counts('subscribers')
                    ->label('Subscribers'),
                TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'manual',
                        'warning' => 'dynamic',
                        'success' => 'all_members',
                    ]),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('import_csv')
                    ->label('Import CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('csv_file')
                            ->disk('local')
                            ->directory('newsletter-imports')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (NewsletterList $record, array $data) {
                        // Logic to handle CSV import would go here
                        // For now, just a placeholder notification
                        \Filament\Notifications\Notification::make()
                            ->title('Import started')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterLists::route('/'),
            'create' => Pages\CreateNewsletterList::route('/create'),
            'edit' => Pages\EditNewsletterList::route('/{record}/edit'),
        ];
    }
}
