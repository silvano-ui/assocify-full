<?php

namespace Database\Seeders;

use App\Core\Permissions\RoleTemplate;
use Illuminate\Database\Seeder;

class RoleTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            'presidente' => [
                'name' => 'Presidente',
                'description' => 'Accesso completo a tutte le funzionalità',
                'permissions' => ['*'], // Logic needs to handle wildcard or list all
                // For now, let's list "all" by query or assume logic handles wildcard.
                // The PermissionManager::createRoleFromTemplate uses specific list. 
                // So I should list all permissions or have a mechanism to expand '*'.
                // Let's assume for this seeder I'll fetch all permissions if '*' is used, 
                // or just keep '*' if the system supports it. 
                // The PermissionManager uses: foreach ($template->permissions ... TenantRolePermission::create
                // So I must expand '*' here or in Manager. 
                // Manager implementation: foreach ($template->permissions as $permissionSlug)
                // So I should expand it here.
            ],
            'segretario' => [
                'name' => 'Segretario',
                'description' => 'Gestione soci, documenti, newsletter',
                'permissions' => [
                    'members.view', 'members.create', 'members.edit', 'members.delete', 'members.export', 'members.import',
                    'documents.view', 'documents.upload', 'documents.edit', 'documents.delete', 'documents.sign', 'documents.manage_templates',
                    'newsletter.view', 'newsletter.create', 'newsletter.send', 'newsletter.manage_templates',
                    'settings.view'
                ],
            ],
            'tesoriere' => [
                'name' => 'Tesoriere',
                'description' => 'Gestione pagamenti e visualizzazione soci',
                'permissions' => [
                    'payments.view', 'payments.create', 'payments.edit', 'payments.delete', 'payments.refund', 'payments.export',
                    'members.view'
                ],
            ],
            'consigliere' => [
                'name' => 'Consigliere',
                'description' => 'Visualizzazione di tutto, nessuna modifica',
                'permissions' => [
                    'members.view', 'events.view', 'payments.view', 'documents.view', 'chat.view', 'gallery.view', 'newsletter.view', 'settings.view'
                ],
            ],
            'allenatore' => [
                'name' => 'Allenatore',
                'description' => 'Gestione eventi e visualizzazione soci',
                'permissions' => [
                    'events.view', 'events.create', 'events.edit', 'events.delete', 'events.manage_registrations', 'events.check_attendance',
                    'members.view'
                ],
            ],
            'socio' => [
                'name' => 'Socio',
                'description' => 'Visualizzazione propri dati e chat',
                'permissions' => [
                    'chat.view', 'chat.send'
                ],
            ],
        ];

        // Fetch all permissions for 'presidente' expansion
        $allPermissions = \App\Core\Permissions\Permission::pluck('slug')->toArray();

        foreach ($templates as $slug => $data) {
            $perms = $data['permissions'];
            if ($perms === ['*']) {
                $perms = $allPermissions;
            }

            RoleTemplate::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'permissions' => $perms,
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );
        }
    }
}
