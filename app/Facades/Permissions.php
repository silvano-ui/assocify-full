<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasPermission(string $permission, ?int $userId = null, ?int $tenantId = null)
 * @method static bool hasAnyPermission(array $permissions, ?int $userId = null)
 * @method static bool hasAllPermissions(array $permissions, ?int $userId = null)
 * @method static array getUserPermissions(?int $userId = null, ?int $tenantId = null)
 * @method static \Illuminate\Support\Collection getUserRoles(?int $userId = null, ?int $tenantId = null)
 * @method static \App\Core\Permissions\UserTenantRole assignRole(int $userId, int $roleId, ?int $assignedBy = null)
 * @method static bool removeRole(int $userId, int $roleId)
 * @method static bool hasRole(string $roleSlug, ?int $userId = null)
 * @method static bool canManageRole(int $managerRoleLevel, int $targetRoleLevel)
 * @method static \App\Core\Permissions\TenantRole createRoleFromTemplate(string $templateSlug, int $tenantId, ?array $overrides = [])
 * 
 * @see \App\Core\Permissions\PermissionManager
 */
class Permissions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Core\Permissions\PermissionManager::class;
    }
}
