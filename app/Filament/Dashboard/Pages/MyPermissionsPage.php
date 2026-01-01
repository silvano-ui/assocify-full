<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use App\Facades\Permissions;
use App\Core\Permissions\Permission;
use Illuminate\Support\Facades\Auth;

class MyPermissionsPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-finger-print';

    protected string $view = 'filament.dashboard.pages.my-permissions-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Account';
    
    protected static ?string $title = 'My Permissions';

    public $groupedPermissions = [];
    public $userRoles = [];

    public function mount()
    {
        $this->userRoles = Permissions::getUserRoles(Auth::id(), Auth::user()->tenant_id);
        $userPermissions = Permissions::getUserPermissions(Auth::id(), Auth::user()->tenant_id);
        
        // Get all system permissions to show what is available vs what user has
        // Or just show what user has?
        // "Mostra all'utente loggato i suoi ruoli e permessi. Raggruppati per modulo. Icone per permessi attivi/non attivi"
        // Implies showing all potential permissions and indicating status.
        
        $allPermissions = Permission::all();
        
        $this->groupedPermissions = $allPermissions->groupBy('module')->map(function ($permissions) use ($userPermissions) {
            return $permissions->map(function ($permission) use ($userPermissions) {
                $permission->has_permission = in_array($permission->slug, $userPermissions);
                return $permission;
            });
        });
    }
}
