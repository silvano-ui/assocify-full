<?php

namespace Modules\Payments\Filament\Resources;

use Modules\Payments\Filament\Resources\SubscriptionRenewalResource\Pages;
use Modules\Payments\Entities\SubscriptionRenewal;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionRenewalResource extends Resource
{
    protected static ?string $model = SubscriptionRenewal::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrow-path';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Payments';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('member_profile_id')
                    ->relationship('memberProfile', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->required(),
                Forms\Components\Select::make('member_category_id')
                    ->relationship('memberCategory', 'name')
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('memberProfile.first_name')
                    ->label('Member')
                    ->formatStateUsing(fn ($record) => "{$record->memberProfile->first_name} {$record->memberProfile->last_name}")
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('amount')->money('eur'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'expired',
                        'warning' => 'pending',
                        'success' => 'paid',
                    ]),
                Tables\Columns\TextColumn::make('due_date')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                    ]),
                Tables\Filters\SelectFilter::make('year'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionRenewals::route('/'),
            'create' => Pages\CreateSubscriptionRenewal::route('/create'),
            'edit' => Pages\EditSubscriptionRenewal::route('/{record}/edit'),
        ];
    }
}
