<?php

namespace Modules\WhiteLabel\Filament\Resources;

use Modules\WhiteLabel\Filament\Resources\TenantBrandingResource\Pages;
use Modules\WhiteLabel\Entities\TenantBranding;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class TenantBrandingResource extends Resource
{
    protected static ?string $model = TenantBranding::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-color-swatch';

    protected static string | \UnitEnum | null $navigationGroup = 'White Label';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // SuperAdmin sempre accesso
        if (!$user->tenant_id) return true;
        
        // Tenant deve avere feature whitelabel
        return function_exists('has_feature') && (has_feature('whitelabel.basic') || has_feature('whitelabel.full'));
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Logos & Icons')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo (Light)')
                            ->image()
                            ->directory('branding/logos'),
                        FileUpload::make('logo_dark_path')
                            ->label('Logo (Dark)')
                            ->image()
                            ->directory('branding/logos'),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->directory('branding/favicons'),
                    ])->columns(3),
                Forms\Components\Section::make('Colors')
                    ->schema([
                        ColorPicker::make('primary_color'),
                        ColorPicker::make('secondary_color'),
                        ColorPicker::make('accent_color'),
                        ColorPicker::make('success_color'),
                        ColorPicker::make('warning_color'),
                        ColorPicker::make('danger_color'),
                        ColorPicker::make('background_color'),
                        ColorPicker::make('sidebar_color'),
                        ColorPicker::make('text_color'),
                    ])->columns(3),
                Forms\Components\Section::make('Typography')
                    ->schema([
                        TextInput::make('font_family'),
                        TextInput::make('font_url')
                            ->url(),
                    ])->columns(2),
                Forms\Components\Section::make('Advanced Customization')
                    ->schema([
                        Textarea::make('custom_css')
                            ->rows(10),
                        Textarea::make('custom_js')
                            ->rows(10),
                    ]),
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Select::make('theme_mode')
                            ->options([
                                'light' => 'Light',
                                'dark' => 'Dark',
                                'system' => 'System',
                            ])
                            ->default('system'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')->label('Logo'),
                TextColumn::make('primary_color'),
                TextColumn::make('theme_mode'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Usually only one record per tenant, but if multiple allow delete
                \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTenantBrandings::route('/'),
            'create' => Pages\CreateTenantBranding::route('/create'),
            'edit' => Pages\EditTenantBranding::route('/{record}/edit'),
        ];
    }
}
