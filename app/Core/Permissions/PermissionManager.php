<?php

namespace App\Core\Permissions;

use App\Core\Permissions\Permission;
use App\Core\Permissions\RoleTemplate;
use App\Core\Permissions\TenantRole;
use App\Core\Permissions\TenantRolePermission;
use App\Core\Permissions\UserTenantRole;
use App\Core\Users\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PermissionManager
{
    /**
     * Check if user has specific permission in current tenant
     */
    public function hasPermission(string $permission, ?int $userId = null, ?int $tenantId = null): bool
    {
        $userId = $userId ?? Auth::id();
        $tenantId = $tenantId ?? (Auth::user()?->tenant_id);

        if (!$userId || !$tenantId) {
            return false;
        }

        // Get all roles for user in tenant
        $roles = $this->getUserRoles($userId, $tenantId);

        foreach ($roles as $userTenantRole) {
            $role = $userTenantRole->role;
            if (!$role) continue;

            // Check if role has the permission
            // We look at TenantRolePermission table
            $hasPermission = $role->permissions()
                ->where('permission_slug', $permission)
                ->where('granted', true)
                ->exists();

            if ($hasPermission) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has one of the permissions
     */
    public function hasAnyPermission(array $permissions, ?int $userId = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all permissions
     */
    public function hasAllPermissions(array $permissions, ?int $userId = null): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission, $userId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions of the user in the tenant
     */
    public function getUserPermissions(?int $userId = null, ?int $tenantId = null): array
    {
        $userId = $userId ?? Auth::id();
        $tenantId = $tenantId ?? (Auth::user()?->tenant_id);

        if (!$userId || !$tenantId) {
            return [];
        }

        $roles = $this->getUserRoles($userId, $tenantId);
        $permissions = [];

        foreach ($roles as $userTenantRole) {
            $role = $userTenantRole->role;
            if (!$role) continue;

            $rolePermissions = $role->permissions()
                ->where('granted', true)
                ->pluck('permission_slug')
                ->toArray();
            
            $permissions = array_merge($permissions, $rolePermissions);
        }

        return array_unique($permissions);
    }

    /**
     * Get user roles in the tenant
     */
    public function getUserRoles(?int $userId = null, ?int $tenantId = null): Collection
    {
        $userId = $userId ?? Auth::id();
        $tenantId = $tenantId ?? (Auth::user()?->tenant_id);

        if (!$userId || !$tenantId) {
            return collect();
        }

        return UserTenantRole::with('role')
            ->where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->get();
    }

    /**
     * Assign role to user
     */
    public function assignRole(int $userId, int $roleId, ?int $assignedBy = null): UserTenantRole
    {
        $role = TenantRole::findOrFail($roleId);
        
        // Ensure role belongs to user's tenant (or we explicitly pass tenant_id, but usually it's inferred from role or user context)
        // Here we assume the roleId is a TenantRole ID, which already has a tenant_id.
        // We should ensure the user is part of that tenant or intended to be.
        // For simplicity, we trust the input but we could validate.
        
        return UserTenantRole::create([
            'user_id' => $userId,
            'tenant_id' => $role->tenant_id,
            'tenant_role_id' => $roleId,
            'assigned_by' => $assignedBy ?? Auth::id(),
            'assigned_at' => now(),
        ]);
    }

    /**
     * Remove role from user
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        $assignment = UserTenantRole::where('user_id', $userId)
            ->where('tenant_role_id', $roleId)
            ->first();

        if ($assignment) {
            return $assignment->delete();
        }

        return false;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $roleSlug, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        $tenantId = Auth::user()?->tenant_id;

        if (!$userId || !$tenantId) {
            return false;
        }

        return UserTenantRole::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereHas('role', function ($query) use ($roleSlug) {
                $query->where('slug', $roleSlug);
            })
            ->exists();
    }

    /**
     * Check role hierarchy (can manager manage target?)
     */
    public function canManageRole(int $managerRoleLevel, int $targetRoleLevel): bool
    {
        // Higher level number means higher authority? 
        // Usually level 0 is lowest, 100 is highest.
        // Let's assume higher > lower.
        return $managerRoleLevel > $targetRoleLevel;
    }

    /**
     * Create role from template
     */
    public function createRoleFromTemplate(string $templateSlug, int $tenantId, ?array $overrides = []): TenantRole
    {
        $template = RoleTemplate::where('slug', $templateSlug)->firstOrFail();

        $roleData = array_merge([
            'tenant_id' => $tenantId,
            'slug' => $overrides['slug'] ?? $template->slug,
            'name' => $overrides['name'] ?? $template->name,
            'description' => $overrides['description'] ?? $template->description,
            'is_default' => $overrides['is_default'] ?? false,
            'is_system' => $overrides['is_system'] ?? false,
            'hierarchy_level' => $overrides['hierarchy_level'] ?? 0, // Template doesn't strictly have hierarchy, maybe add sort_order as proxy?
        ], $overrides);

        $role = TenantRole::create($roleData);

        // Assign permissions from template
        if (!empty($template->permissions)) {
            foreach ($template->permissions as $permissionSlug) {
                TenantRolePermission::create([
                    'tenant_role_id' => $role->id,
                    'permission_slug' => $permissionSlug,
                    'granted' => true,
                ]);
            }
        }

        return $role;
    }
}
