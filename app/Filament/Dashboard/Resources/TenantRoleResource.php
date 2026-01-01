<?php

namespace App\Filament\Dashboard\Resources;

use App\Core\Permissions\TenantRole;
use App\Core\Permissions\Permission;
use App\Core\Permissions\RoleTemplate;
use App\Core\Permissions\TenantRolePermission;
use App\Filament\Dashboard\Resources\TenantRoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Str;

class TenantRoleResource extends Resource
{
    protected static ?string $model = TenantRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $label = 'Role';
    protected static ?string $pluralLabel = 'Roles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                ColorPicker::make('color'),
                Select::make('icon')
                    ->options([
                        'heroicon-o-user' => 'User',
                        'heroicon-o-user-group' => 'Group',
                        'heroicon-o-shield-check' => 'Shield',
                        'heroicon-o-star' => 'Star',
                        'heroicon-o-academic-cap' => 'Academic',
                        // Add more as needed
                    ])
                    ->searchable(),
                TextInput::make('hierarchy_level')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_default'),
                
                Group::make()
                    ->schema(function () {
                        $modules = Permission::distinct()->pluck('module')->toArray();
                        $schema = [];
                        
                        foreach ($modules as $module) {
                            $schema[] = CheckboxList::make("permissions_module_{$module}")
                                ->label(ucfirst($module) . ' Permissions')
                                ->options(
                                    Permission::where('module', $module)
                                        ->pluck('name', 'slug')
                                )
                                ->default([])
                                ->afterStateHydrated(function (CheckboxList $component, ?TenantRole $record) use ($module) {
                                    if (!$record) return;
                                    
                                    // Fetch permissions for this role that match the module
                                    // Relation: role -> permissions (TenantRolePermission) -> permission_slug
                                    $recordPermissions = $record->permissions()
                                        ->whereIn('permission_slug', Permission::where('module', $module)->pluck('slug'))
                                        ->pluck('permission_slug')
                                        ->toArray();
                                        
                                    $component->state($recordPermissions);
                                })
                                ->dehydrated(false) // Handle saving manually in Page hooks
                                ->columns(2);
                        }
                        
                        return $schema;
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (TenantRole $record) => $record->description),
                TextColumn::make('color')
                    ->formatStateUsing(fn ($state) => $state ? "<span style='color:{$state}'>●</span> {$state}" : null)
                    ->html(),
                IconColumn::make('icon'),
                TextColumn::make('user_roles_count')
                    ->counts('userRoles')
                    ->label('Users'),
                IconColumn::make('is_default')
                    ->boolean(),
                IconColumn::make('is_system')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_system')
                    ->query(fn ($query) => $query->where('is_system', true))
                    ->label('System Roles'),
                Tables\Filters\Filter::make('is_default')
                    ->query(fn ($query) => $query->where('is_default', true))
                    ->label('Default Roles'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->hidden(fn (TenantRole $record) => $record->is_system), // Prevent editing system roles? Or just protect slug?
                    // User requirement: "Protezione: ruoli is_system non modificabili"
                    // If strictly not editable:
                    // ->hidden(fn ($record) => $record->is_system)
                    // Or allow editing some fields but not others.
                    // Let's hide Edit for system roles for safety, or make fields disabled.
                    // Ideally system roles might allow changing color/icon but not permissions/slug.
                    // For now, let's allow edit but maybe disable dangerous fields in form.
                    // Actually, "non modificabili" implies read-only or hidden edit.
                    // Let's hide EditAction for is_system for now, or use `authorize`.
                \Filament\Actions\DeleteAction::make()
                    ->hidden(fn (TenantRole $record) => $record->is_system || !$record->can_be_deleted),
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
            'index' => Pages\ListTenantRoles::route('/'),
            'create' => Pages\CreateTenantRole::route('/create'),
            'edit' => Pages\EditTenantRole::route('/{record}/edit'),
        ];
    }
}
