<?php

namespace Modules\Api\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Api\Entities\ApiOauthClient;
use Modules\Api\Filament\Resources\ApiOauthClientResource\Pages;
use Illuminate\Support\Str;

class ApiOauthClientResource extends Resource
{
    protected static ?string $model = ApiOauthClient::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-server';

    protected static string | \UnitEnum | null $navigationGroup = 'API';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('client_id')
                    ->default(fn () => Str::random(32))
                    ->required()
                    ->readOnly(),
                TextInput::make('client_secret_input') // Virtual field for display/generation
                    ->label('Client Secret')
                    ->default(fn () => Str::random(40))
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateApiOauthClient)
                    ->helperText('Copy this secret now. You won\'t be able to see it again.'),
                TagsInput::make('redirect_uris')
                    ->label('Redirect URIs')
                    ->required()
                    ->placeholder('https://example.com/callback'),
                Select::make('grant_types')
                    ->multiple()
                    ->options([
                        'authorization_code' => 'Authorization Code',
                        'client_credentials' => 'Client Credentials',
                        'refresh_token' => 'Refresh Token',
                        'password' => 'Password',
                        'personal_access' => 'Personal Access',
                    ])
                    ->required(),
                Toggle::make('is_confidential')
                    ->label('Confidential Client')
                    ->default(true)
                    ->helperText('Confidential clients require a client secret.'),
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
                TextColumn::make('client_id')
                    ->label('Client ID')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('redirect_uris')
                    ->label('Redirect URIs')
                    ->badge()
                    ->limitList(2),
                IconColumn::make('is_confidential')
                    ->boolean(),
                IconColumn::make('is_active')
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
                Action::make('regenerate_secret')
                    ->label('Regenerate Secret')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ApiOauthClient $record) {
                        $newSecret = Str::random(40);
                        $record->update(['client_secret_hash' => hash('sha256', $newSecret)]); // Assuming hashing
                        
                        \Filament\Notifications\Notification::make()
                            ->title('New Secret Generated')
                            ->body('Copy it now: ' . $newSecret)
                            ->persistent()
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
            'index' => Pages\ListApiOauthClients::route('/'),
            'create' => Pages\CreateApiOauthClient::route('/create'),
            'edit' => Pages\EditApiOauthClient::route('/{record}/edit'),
        ];
    }
}
