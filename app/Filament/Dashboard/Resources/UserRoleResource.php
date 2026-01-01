<?php

namespace App\Filament\Dashboard\Resources;

use App\Core\Users\User;
use App\Core\Permissions\TenantRole;
use App\Filament\Dashboard\Resources\UserRoleResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class UserRoleResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $label = 'User Role';
    protected static ?string $pluralLabel = 'User Roles';
    
    protected static ?string $slug = 'user-roles';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('users.tenant_id', auth()->user()->tenant_id);
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('email')
                    ->disabled(),
                CheckboxList::make('roles')
                    ->relationship('roles', 'name', function (Builder $query) {
                        return $query->where('tenant_roles.tenant_id', auth()->user()->tenant_id);
                    })
                    ->label('Assigned Roles')
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->separator(','),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name', function (Builder $query) {
                         return $query->where('tenant_roles.tenant_id', auth()->user()->tenant_id);
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListUserRoles::route('/'),
            'edit' => Pages\EditUserRole::route('/{record}/edit'),
        ];
    }
}
