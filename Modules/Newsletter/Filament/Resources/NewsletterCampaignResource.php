<?php

namespace Modules\Newsletter\Filament\Resources;

use Modules\Newsletter\Filament\Resources\NewsletterCampaignResource\Pages;
use Modules\Newsletter\Entities\NewsletterCampaign;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Modules\Newsletter\Entities\NewsletterList;
use Modules\Newsletter\Entities\NewsletterTemplate;

class NewsletterCampaignResource extends Resource
{
    protected static ?string $model = NewsletterCampaign::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | \UnitEnum | null $navigationGroup = 'Newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Campaign Details')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('preview_text')
                                    ->maxLength(255),
                                TextInput::make('from_name')
                                    ->required()
                                    ->default(fn () => auth()->user()->name ?? config('mail.from.name')),
                                TextInput::make('from_email')
                                    ->email()
                                    ->required()
                                    ->default(fn () => auth()->user()->email ?? config('mail.from.address')),
                            ]),
                        Tabs\Tab::make('Content')
                            ->schema([
                                Select::make('template_id')
                                    ->label('Template')
                                    ->options(NewsletterTemplate::pluck('name', 'id'))
                                    ->searchable(),
                                Select::make('list_ids')
                                    ->label('Recipients Lists')
                                    ->multiple()
                                    ->options(NewsletterList::pluck('name', 'id'))
                                    ->searchable(),
                                Textarea::make('segment_filters')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('Schedule')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'scheduled' => 'Scheduled',
                                        'sending' => 'Sending',
                                        'sent' => 'Sent',
                                        'paused' => 'Paused',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                DateTimePicker::make('send_at')
                                    ->label('Scheduled For'),
                            ]),
                        Tabs\Tab::make('Statistics')
                            ->schema([
                                TextInput::make('sent_count')
                                    ->label('Sent')
                                    ->disabled(),
                                // Add more stats fields or widgets here
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'info' => 'sending',
                        'success' => 'sent',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('sent_count')
                    ->label('Sent')
                    ->sortable(),
                TextColumn::make('send_at')
                    ->label('Scheduled')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('send_now')
                    ->label('Send Now')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function (NewsletterCampaign $record) {
                        $record->update(['status' => 'sending', 'started_at' => now()]);
                        \Filament\Notifications\Notification::make()
                            ->title('Sending started')
                            ->success()
                            ->send();
                    }),
                Action::make('schedule')
                    ->label('Schedule')
                    ->icon('heroicon-o-clock')
                    ->form([
                        DateTimePicker::make('scheduled_at')
                            ->required(),
                    ])
                    ->action(function (NewsletterCampaign $record, array $data) {
                        $record->update(['status' => 'scheduled', 'send_at' => $data['scheduled_at']]);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign scheduled')
                            ->success()
                            ->send();
                    }),
                Action::make('pause')
                    ->label('Pause')
                    ->icon('heroicon-o-pause')
                    ->visible(fn (NewsletterCampaign $record) => $record->status === 'sending')
                    ->action(function (NewsletterCampaign $record) {
                        $record->update(['status' => 'paused']);
                    }),
                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (NewsletterCampaign $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name . ' (Copy)';
                        $newRecord->status = 'draft';
                        $newRecord->push();
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign duplicated')
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
            'index' => Pages\ListNewsletterCampaigns::route('/'),
            'create' => Pages\CreateNewsletterCampaign::route('/create'),
            'edit' => Pages\EditNewsletterCampaign::route('/{record}/edit'),
        ];
    }
}
