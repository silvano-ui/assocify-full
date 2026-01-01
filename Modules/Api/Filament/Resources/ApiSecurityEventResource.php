<?php

namespace Modules\Api\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Modules\Api\Entities\ApiSecurityEvent;
use Modules\Api\Filament\Resources\ApiSecurityEventResource\Pages;

class ApiSecurityEventResource extends Resource
{
    protected static ?string $model = ApiSecurityEvent::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-exclamation';

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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Read only
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'auth_failure' => 'warning',
                        'rate_limit_exceeded' => 'warning',
                        'ip_blocked' => 'danger',
                        'suspicious_activity' => 'danger',
                        'token_revoked' => 'gray',
                        'permission_denied' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                TextColumn::make('user_agent')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'info',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event_type')
                    ->options([
                        'auth_failure' => 'Auth Failure',
                        'rate_limit_exceeded' => 'Rate Limit Exceeded',
                        'ip_blocked' => 'IP Blocked',
                        'suspicious_activity' => 'Suspicious Activity',
                        'token_revoked' => 'Token Revoked',
                        'permission_denied' => 'Permission Denied',
                    ]),
            ])
            ->actions([
                Action::make('block_ip')
                    ->label('Block IP')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ApiSecurityEvent $record) {
                        // Create a blocking event
                        ApiSecurityEvent::create([
                            'event_type' => 'ip_blocked',
                            'ip_address' => $record->ip_address,
                            'severity' => 'high',
                            'details' => json_encode(['reason' => 'Manual block from admin']),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('IP ' . $record->ip_address . ' blocked')
                            ->success()
                            ->send();
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
            'index' => Pages\ListApiSecurityEvents::route('/'),
        ];
    }
}
