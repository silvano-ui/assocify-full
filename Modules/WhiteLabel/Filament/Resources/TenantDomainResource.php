<?php

namespace Modules\WhiteLabel\Filament\Resources;

use Modules\WhiteLabel\Filament\Resources\TenantDomainResource\Pages;
use Modules\WhiteLabel\Entities\TenantDomain;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;

class TenantDomainResource extends Resource
{
    protected static ?string $model = TenantDomain::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';

    protected static ?string $navigationGroup = 'White Label';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // SuperAdmin sempre accesso
        if (!$user->tenant_id) return true;
        
        // Tenant deve avere feature whitelabel
        return function_exists('has_feature') && (has_feature('whitelabel.basic') || has_feature('whitelabel.full'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->required()
                    ->unique(ignoreRecord: true),
                Toggle::make('is_primary')
                    ->label('Primary Domain'),
                Toggle::make('is_verified')
                    ->label('Verified')
                    ->disabled(), // Should be verified via action
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')->searchable(),
                IconColumn::make('is_primary')->boolean(),
                IconColumn::make('is_verified')->boolean(),
                TextColumn::make('ssl_status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (TenantDomain $record) {
                        // Call verification service
                    })
                    ->visible(fn (TenantDomain $record) => !$record->is_verified),
                Action::make('request_ssl')
                    ->label('Request SSL')
                    ->icon('heroicon-o-lock-closed')
                    ->action(function (TenantDomain $record) {
                        // Call SSL service
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTenantDomains::route('/'),
            'create' => Pages\CreateTenantDomain::route('/create'),
            'edit' => Pages\EditTenantDomain::route('/{record}/edit'),
        ];
    }
}
