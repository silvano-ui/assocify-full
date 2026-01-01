<?php

namespace App\Filament\Dashboard\Resources\TenantRoleResource\Pages;

use App\Filament\Dashboard\Resources\TenantRoleResource;
use App\Core\Permissions\RoleTemplate;
use App\Core\Permissions\TenantRolePermission;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use App\Facades\Permissions;

class ListTenantRoles extends ListRecords
{
    protected static string $resource = TenantRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            Actions\Action::make('create_from_template')
                ->label('Create from Template')
                ->icon('heroicon-o-document-duplicate')
                ->form([
                    Select::make('template_slug')
                        ->label('Template')
                        ->options(RoleTemplate::where('is_active', true)->pluck('name', 'slug'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $templateSlug = $data['template_slug'];
                    // Use Facade or Manager
                    $tenantId = auth()->user()->tenant_id;
                    
                    try {
                        Permissions::createRoleFromTemplate($templateSlug, $tenantId);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Role created from template')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error creating role')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
