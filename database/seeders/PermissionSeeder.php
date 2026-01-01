<?php

namespace Database\Seeders;

use App\Core\Permissions\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'members' => ['view', 'create', 'edit', 'delete', 'export', 'import'],
            'events' => ['view', 'create', 'edit', 'delete', 'manage_registrations', 'check_attendance'],
            'payments' => ['view', 'create', 'edit', 'delete', 'refund', 'export'],
            'documents' => ['view', 'upload', 'edit', 'delete', 'sign', 'manage_templates'],
            'chat' => ['view', 'send', 'moderate', 'delete_others', 'manage_channels'],
            'gallery' => ['view', 'upload', 'edit', 'delete', 'manage_albums'],
            'newsletter' => ['view', 'create', 'send', 'manage_templates'],
            'settings' => ['view', 'edit', 'manage_users', 'manage_roles', 'billing'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['slug' => "{$module}.{$action}"],
                    [
                        'name' => Str::title("{$action} {$module}"),
                        'description' => "Allow user to {$action} in {$module} module",
                        'module' => $module,
                        'category' => 'general',
                    ]
                );
            }
        }
    }
}
