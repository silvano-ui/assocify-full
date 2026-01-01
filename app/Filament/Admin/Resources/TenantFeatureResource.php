<?php

namespace App\Filament\Admin\Resources;

use App\Core\Features\Feature;
use App\Core\Features\TenantFeature;
use App\Core\Tenant\Tenant;
use App\Facades\Features;
use App\Filament\Admin\Resources\TenantFeatureResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

class TenantFeatureResource extends Resource
{
    protected static ?string $model = TenantFeature::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(Tenant::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->disabledOn('edit'),
                
                Select::make('feature_slug')
                    ->label('Feature')
                    ->options(Feature::pluck('name', 'slug'))
                    ->required()
                    ->searchable()
                    ->disabledOn('edit'),

                Select::make('source')
                    ->options([
                        'plan' => 'Plan',
                        'addon' => 'Addon',
                        'trial' => 'Trial',
                        'gift' => 'Gift',
                        'custom' => 'Custom',
                    ])
                    ->required(),

                Toggle::make('enabled')
                    ->default(true),

                TextInput::make('limit_value')
                    ->numeric(),
                
                TextInput::make('used_value')
                    ->numeric()
                    ->disabled(), // Usage should not be manually edited usually, or maybe yes for correction?

                DatePicker::make('expires_at'),
                
                Textarea::make('notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->sortable()->searchable(),
                TextColumn::make('feature_slug')->label('Feature')->sortable()->searchable(),
                TextColumn::make('source')->badge()->color(fn (string $state): string => match ($state) {
                    'plan' => 'gray',
                    'addon' => 'success',
                    'trial' => 'warning',
                    'gift' => 'info',
                    'custom' => 'danger',
                }),
                IconColumn::make('enabled')->boolean(),
                TextColumn::make('limit_value'),
                TextColumn::make('used_value'),
                TextColumn::make('expires_at')->date(),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->relationship('tenant', 'name'),
                SelectFilter::make('source')
                    ->options([
                        'plan' => 'Plan',
                        'addon' => 'Addon',
                        'trial' => 'Trial',
                        'gift' => 'Gift',
                        'custom' => 'Custom',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Action::make('activate_trial')
                    ->label('Activate Trial')
                    ->icon('heroicon-o-clock')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('days')->numeric()->default(14)->required(),
                    ])
                    ->action(function (TenantFeature $record, array $data) {
                        $record->update([
                            'source' => 'trial',
                            'is_trial' => true,
                            'trial_ends_at' => now()->addDays($data['days']),
                            'enabled' => true,
                            'expires_at' => now()->addDays($data['days']),
                        ]);
                    })
                    ->visible(fn (TenantFeature $record) => !$record->is_trial),

                Action::make('gift_feature')
                    ->label('Gift Feature')
                    ->icon('heroicon-o-gift')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('days')->label('Gift Duration (Days)')->numeric()->default(30)->required(),
                        TextInput::make('limit_value')->label('Custom Limit')->numeric(),
                    ])
                    ->action(function (TenantFeature $record, array $data) {
                        $record->update([
                            'source' => 'gift',
                            'expires_at' => now()->addDays($data['days']),
                            'limit_value' => $data['limit_value'] ?? $record->limit_value,
                            'enabled' => true,
                        ]);
                    })
                    ->visible(fn (TenantFeature $record) => $record->source !== 'gift'),

                Action::make('revoke')
                    ->label('Revoke')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (TenantFeature $record) => $record->update(['enabled' => false])),
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
            'index' => Pages\ListTenantFeatures::route('/'),
            'create' => Pages\CreateTenantFeature::route('/create'),
            'edit' => Pages\EditTenantFeature::route('/{record}/edit'),
        ];
    }
}
