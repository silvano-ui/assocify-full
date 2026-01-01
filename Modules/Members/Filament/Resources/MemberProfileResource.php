<?php

namespace Modules\Members\Filament\Resources;

use Modules\Members\Filament\Resources\MemberProfileResource\Pages;
use Modules\Members\Entities\MemberProfile;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Core\ModuleManager\ModuleManager;
use Filament\Facades\Filament;

class MemberProfileResource extends Resource
{
    protected static ?string $model = MemberProfile::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Members';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('member_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\TextInput::make('birth_place')
                    ->maxLength(255),
                Forms\Components\TextInput::make('fiscal_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('province')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip')
                    ->maxLength(255),
                Forms\Components\TextInput::make('country')
                    ->default('IT')
                    ->maxLength(255),
                Forms\Components\Select::make('document_type')
                    ->options([
                        'id_card' => 'Carta d\'Identità',
                        'passport' => 'Passaporto',
                        'driving_license' => 'Patente',
                        'other' => 'Altro',
                    ]),
                Forms\Components\TextInput::make('document_number')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('document_expires'),
                Forms\Components\TextInput::make('emergency_contact')
                    ->maxLength(255),
                Forms\Components\TextInput::make('emergency_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('custom_fields')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('member_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fiscal_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMemberProfiles::route('/'),
            'create' => Pages\CreateMemberProfile::route('/create'),
            'edit' => Pages\EditMemberProfile::route('/{record}/edit'),
        ];
    }
}
