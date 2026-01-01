<?php

use App\Facades\Features;
use App\Facades\Permissions;

if (!function_exists('has_feature')) {
    function has_feature(string $slug): bool
    {
        return Features::hasFeature($slug);
    }
}

if (!function_exists('can_use_feature')) {
    function can_use_feature(string $slug, int $qty = 1): bool
    {
        return Features::canUse($slug, $qty)['allowed'];
    }
}

if (!function_exists('feature_limit')) {
    function feature_limit(string $slug): ?int
    {
        return Features::getLimit($slug);
    }
}

if (!function_exists('feature_remaining')) {
    function feature_remaining(string $slug): ?int
    {
        return Features::getRemaining($slug);
    }
}

if (!function_exists('has_permission')) {
    function has_permission(string $permission): bool
    {
        return Permissions::hasPermission($permission);
    }
}

if (!function_exists('has_role')) {
    function has_role(string $role): bool
    {
        return Permissions::hasRole($role);
    }
}

if (!function_exists('user_permissions')) {
    function user_permissions(): array
    {
        return Permissions::getUserPermissions();
    }
}

if (!function_exists('user_roles')) {
    function user_roles(): \Illuminate\Support\Collection
    {
        return Permissions::getUserRoles();
    }
}
