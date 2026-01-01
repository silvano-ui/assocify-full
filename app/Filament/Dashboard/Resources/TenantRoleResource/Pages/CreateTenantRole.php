<?php

namespace App\Filament\Dashboard\Resources\TenantRoleResource\Pages;

use App\Filament\Dashboard\Resources\TenantRoleResource;
use App\Core\Permissions\TenantRolePermission;
use App\Core\Permissions\Permission;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTenantRole extends CreateRecord
{
    protected static string $resource = TenantRoleResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $data = $this->data; // This contains the form data, including permissions_module_* keys
        
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
