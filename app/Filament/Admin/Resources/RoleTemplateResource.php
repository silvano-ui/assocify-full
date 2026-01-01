<?php

namespace App\Filament\Admin\Resources;

use App\Core\Permissions\RoleTemplate;
use App\Core\Permissions\Permission;
use App\Filament\Admin\Resources\RoleTemplateResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class RoleTemplateResource extends Resource
{
    protected static ?string $model = RoleTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
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
                                ->afterStateHydrated(function (CheckboxList $component, ?RoleTemplate $record) use ($module) {
                                    if (!$record) return;
                                    
                                    // Filter record permissions that belong to this module
                                    $modulePermissions = Permission::where('module', $module)->pluck('slug')->toArray();
                                    $selected = array_intersect($record->permissions ?? [], $modulePermissions);
                                    $component->state($selected);
                                })
                                ->dehydrated(false) // Handle saving manually or use a mutator? 
                                // Actually, RoleTemplate has 'permissions' json column. 
                                // We need to aggregate all these checkbox lists into one array.
                                // Filament doesn't easily map multiple fields to one JSON column without custom logic.
                                // A better approach might be a single CheckboxList with grouping if supported, 
                                // or using a custom save hook.
                                // For simplicity let's try a single CheckboxList but it might be huge.
                                // Or use 'permissions' field and group options.
                                ;
                        }
                        
                        // Alternative: Single CheckboxList
                        return [
                            CheckboxList::make('permissions')
                                ->label('Permissions')
                                ->options(function () {
                                    $permissions = Permission::all();
                                    $options = [];
                                    foreach ($permissions as $permission) {
                                        $group = ucfirst($permission->module);
                                        $options[$group][$permission->slug] = $permission->name;
                                    }
                                    return $options;
                                })
                                ->columns(2)
                                ->columnSpanFull()
                                ->searchable()
                                ->bulkToggleable(),
                        ];
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
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
            'index' => Pages\ListRoleTemplates::route('/'),
            'create' => Pages\CreateRoleTemplate::route('/create'),
            'edit' => Pages\EditRoleTemplate::route('/{record}/edit'),
        ];
    }
}
