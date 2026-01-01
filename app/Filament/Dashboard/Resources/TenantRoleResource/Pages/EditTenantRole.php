<?php

namespace App\Filament\Dashboard\Resources\TenantRoleResource\Pages;

use App\Filament\Dashboard\Resources\TenantRoleResource;
use App\Core\Permissions\TenantRolePermission;
use App\Core\Permissions\Permission;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTenantRole extends EditRecord
{
    protected static string $resource = TenantRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;
        
        // Sync permissions
        // First delete existing permissions
        // Note: In a real world scenario we might want to be more careful, but here we replace all.
        // Or we can use sync if we had a BelongsToMany.
        
        // We need to know which modules were present in the form to only update those?
        // Or assume all modules are present.
        // If we only delete permissions that are "manageable", we avoid deleting "system" permissions if any?
        // But TenantRolePermission is purely user managed for Tenant Roles.
        
        $record->permissions()->delete(); // Remove all permissions
        
        $modules = Permission::distinct()->pluck('module')->toArray();
        
        foreach ($modules as $module) {
            $key = "permissions_module_{$module}";
            if (isset($data[$key]) && is_array($data[$key])) {
                foreach ($data[$key] as $permissionSlug) {
                    TenantRolePermission::create([
                        'tenant_role_id' => $record->id,
                        'permission_slug' => $permissionSlug,
                        'granted' => true,
                    ]);
                }
            }
        }
    }
}
