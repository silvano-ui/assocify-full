<?php

namespace Modules\Localization\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\TenantTranslation;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Filament\Resources\TenantTranslationResource\Pages;
use Illuminate\Database\Eloquent\Model;

class TenantTranslationResource extends Resource
{
    protected static ?string $model = TenantTranslation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-language';

    protected static string | \UnitEnum | null $navigationGroup = 'Localization';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('group')
                    ->disabled(),
                Forms\Components\TextInput::make('key')
                    ->disabled(),
                Forms\Components\Textarea::make('base_value')
                    ->label('Base Value')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn (Translation $record) => $record->value),
                Forms\Components\Textarea::make('value')
                    ->label('Override Value')
                    ->hint('Leave empty to use base value'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Base Value')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('override_value')
                    ->label('Override')
                    ->limit(30)
                    ->getStateUsing(function (Translation $record) {
                        $tenantId = filament()->getTenant()->id;
                        $override = TenantTranslation::where('tenant_id', $tenantId)
                            ->where('locale', $record->locale) // Wait, Translation has locale. Which locale are we viewing?
                            // We need to filter by locale.
                            ->where('group', $record->group)
                            ->where('key', $record->key)
                            ->first();
                        return $override ? $override->value : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options(fn () => Translation::distinct()->pluck('group', 'group')),
                Tables\Filters\SelectFilter::make('locale')
                    ->options(fn () => \Modules\Localization\Entities\Language::pluck('name', 'code'))
                    ->default(config('app.locale')),
            ])
            ->actions([
                EditAction::make()
                    ->using(function (Translation $record, array $data): Model {
                        $tenantId = filament()->getTenant()->id;
                        
                        // Create or update TenantTranslation
                        return TenantTranslation::updateOrCreate(
                            [
                                'tenant_id' => $tenantId,
                                'locale' => $record->locale,
                                'group' => $record->group,
                                'key' => $record->key,
                            ],
                            [
                                'value' => $data['value'],
                                'is_auto_translated' => false, // Manual override
                            ]
                        );
                    })
                    ->mountUsing(function (Schema $form, Translation $record) {
                        $tenantId = filament()->getTenant()->id;
                        $override = TenantTranslation::where('tenant_id', $tenantId)
                            ->where('locale', $record->locale)
                            ->where('group', $record->group)
                            ->where('key', $record->key)
                            ->first();
                        
                        $form->fill([
                            'group' => $record->group,
                            'key' => $record->key,
                            'base_value' => $record->value,
                            'value' => $override ? $override->value : null,
                        ]);
                    }),
            ])
            ->bulkActions([
                // No bulk delete for base translations
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantTranslations::route('/'),
            // 'edit' => Pages\EditTenantTranslation::route('/{record}/edit'), // We use modal or simple edit
        ];
    }
}
