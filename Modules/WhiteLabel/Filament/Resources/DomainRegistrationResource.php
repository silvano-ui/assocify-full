<?php

namespace Modules\WhiteLabel\Filament\Resources;

use Modules\WhiteLabel\Filament\Resources\DomainRegistrationResource\Pages;
use Modules\WhiteLabel\Entities\DomainRegistration;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;

class DomainRegistrationResource extends Resource
{
    protected static ?string $model = DomainRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

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
                    ->maxLength(255)
                    ->disabled(fn ($record) => $record !== null), // Domain cannot be changed after creation
                Select::make('registration_years')
                    ->options([
                        1 => '1 Year',
                        2 => '2 Years',
                        3 => '3 Years',
                        5 => '5 Years',
                        10 => '10 Years',
                    ])
                    ->default(1)
                    ->required(),
                Toggle::make('auto_renew')
                    ->default(true),
                // Contact info would be a complex form or relationship
                // Keeping it simple for now
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')->searchable()->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending_registration',
                        'success' => 'registered',
                        'danger' => 'failed',
                        'secondary' => 'transferred',
                    ]),
                TextColumn::make('expires_at')->date()->sortable(),
                IconColumn::make('auto_renew')->boolean(),
                TextColumn::make('registrar_provider'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('renew')
                    ->label('Renew')
                    ->icon('heroicon-o-refresh')
                    ->action(function (DomainRegistration $record) {
                        // Call service to renew
                        // For now just a notification
                        // In real app, redirect to renewal page or modal
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ListDomainRegistrations::route('/'),
            'create' => Pages\CreateDomainRegistration::route('/create'),
            'edit' => Pages\EditDomainRegistration::route('/{record}/edit'),
        ];
    }
}
