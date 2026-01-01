<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use App\Facades\Permissions;
use App\Core\Permissions\Permission;
use Illuminate\Support\Facades\Auth;

class MyPlanPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'My Plan';
    protected static ?string $title = 'My Plan & Features';
    protected static ?string $slug = 'my-plan';
    protected string $view = 'filament.dashboard.pages.my-plan-page';

    public $groupedPermissions = [];
    public $userRoles = [];

    public function mount(): void
    {
        $this->userRoles = Permissions::getUserRoles(Auth::id(), Auth::user()->tenant_id);
        
        $userPermissionSlugs = Permissions::getUserPermissions(Auth::id(), Auth::user()->tenant_id);
        
        // Fetch permission objects that match the slugs the user has
        $permissions = Permission::whereIn('slug', $userPermissionSlugs)->get();
        
        // Group by module and map to just the name for display
        $this->groupedPermissions = $permissions->groupBy('module')->map(function ($group) {
            return $group->pluck('name');
        });
    }
}
