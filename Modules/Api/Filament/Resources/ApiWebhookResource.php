<?php

namespace Modules\Api\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Api\Entities\ApiWebhook;
use Modules\Api\Filament\Resources\ApiWebhookResource\Pages;
use Illuminate\Support\Str;

class ApiWebhookResource extends Resource
{
    protected static ?string $model = ApiWebhook::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string | \UnitEnum | null $navigationGroup = 'API';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // SuperAdmin (no tenant_id) always has access
        if (!$user->tenant_id) return true;
        
        // Tenant users need api.access feature
        return function_exists('has_feature') && has_feature('api.access');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('Payload URL')
                    ->url()
                    ->required()
                    ->maxLength(255),
                TextInput::make('secret')
                    ->default(fn () => Str::random(32))
                    ->required()
                    ->readOnly()
                    ->helperText('Secret used to sign the webhook payload (HMAC SHA256)'),
                CheckboxList::make('events')
                    ->options([
                        'member.created' => 'Member Created',
                        'member.updated' => 'Member Updated',
                        'member.deleted' => 'Member Deleted',
                        'payment.succeeded' => 'Payment Succeeded',
                        'payment.failed' => 'Payment Failed',
                    ])
                    ->columns(2)
                    ->required(),
                KeyValue::make('headers')
                    ->keyLabel('Header Name')
                    ->valueLabel('Header Value'),
                TextInput::make('retry_count')
                    ->numeric()
                    ->default(3)
                    ->required(),
                TextInput::make('timeout')
                    ->numeric()
                    ->default(10)
                    ->suffix('seconds')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('events')
                    ->formatStateUsing(fn ($state) => count($state ?? []) . ' events')
                    ->badge(),
                TextColumn::make('last_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('failure_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('test')
                    ->label('Test Webhook')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function (ApiWebhook $record) {
                        // Logic to send ping
                        try {
                            // In a real scenario, we would dispatch a job or use Http client
                            // For demo:
                            $response = \Illuminate\Support\Facades\Http::timeout(5)
                                ->post($record->url, [
                                    'event' => 'ping',
                                    'timestamp' => now()->toIso8601String(),
                                ]);
                            
                            if ($response->successful()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Test successful')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Test failed: ' . $response->status())
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Test failed: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiWebhooks::route('/'),
            'create' => Pages\CreateApiWebhook::route('/create'),
            'edit' => Pages\EditApiWebhook::route('/{record}/edit'),
        ];
    }
}
