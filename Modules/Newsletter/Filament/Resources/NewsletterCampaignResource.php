<?php

namespace Modules\Newsletter\Filament\Resources;

use Modules\Newsletter\Filament\Resources\NewsletterCampaignResource\Pages;
use Modules\Newsletter\Entities\NewsletterCampaign;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class NewsletterCampaignResource extends Resource
{
    protected static ?string $model = NewsletterCampaign::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | \UnitEnum | null $navigationGroup = 'Newsletter';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('preview_text')
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('from_name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('from_email')
                    ->email()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('reply_to')
                    ->email(),
                \Filament\Forms\Components\Select::make('template_id')
                    ->relationship('template', 'name'),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'paused' => 'Paused',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('draft'),
                \Filament\Forms\Components\DateTimePicker::make('send_at')
                    ->label('Schedule Send'),
                \Filament\Forms\Components\Textarea::make('html_content')
                    ->label('Email Content')
                    ->rows(10)
                    ->columnSpanFull(),
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
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('send_now')
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
                \Filament\Actions\Action::make('schedule')
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
                \Filament\Actions\Action::make('pause')
                    ->label('Pause')
                    ->icon('heroicon-o-pause')
                    ->visible(fn (NewsletterCampaign $record) => $record->status === 'sending')
                    ->action(function (NewsletterCampaign $record) {
                        $record->update(['status' => 'paused']);
                    }),
                \Filament\Actions\Action::make('duplicate')
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
                \Filament\Actions\DeleteBulkAction::make(),
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
