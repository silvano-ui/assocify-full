<?php

namespace Modules\Api\Filament\Resources;

use Modules\Api\Filament\Resources\ApiKeyResource\Pages;
use Modules\Api\Entities\ApiKey;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Str;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';
    
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
                Select::make('type')
                    ->options([
                        'live' => 'Live',
                        'sandbox' => 'Sandbox',
                    ])
                    ->required()
                    ->default('live'),
                CheckboxList::make('permissions')
                    ->options([
                        'read' => 'Read',
                        'write' => 'Write',
                        'delete' => 'Delete',
                    ])
                    ->columns(3),
                TextInput::make('rate_limit_per_minute')
                    ->numeric()
                    ->default(60)
                    ->required(),
                TextInput::make('rate_limit_per_day')
                    ->numeric()
                    ->default(10000)
                    ->required(),
                Repeater::make('allowed_ips')
                    ->schema([
                        TextInput::make('ip')->required()->ip(),
                    ])
                    ->columnSpanFull(),
                DateTimePicker::make('expires_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'live' => 'success',
                        'sandbox' => 'warning',
                    }),
                TextColumn::make('rate_limit_per_minute')->label('Rate Limit (Min)'),
                TextColumn::make('last_used_at')->dateTime()->sortable(),
                TextColumn::make('is_active')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'live' => 'Live',
                        'sandbox' => 'Sandbox',
                    ]),
                SelectFilter::make('is_active')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('view_key')
                    ->label('View Key')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('API Key Details')
                    ->modalContent(fn (ApiKey $record) => view('api::filament.resources.api-key-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Action::make('regenerate_secret')
                    ->label('Regenerate Secret')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ApiKey $record) {
                        $newSecret = Str::random(64);
                        $record->update(['secret_hash' => $newSecret]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Secret Regenerated')
                            ->body('New secret: ' . $newSecret)
                            ->persistent()
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
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
