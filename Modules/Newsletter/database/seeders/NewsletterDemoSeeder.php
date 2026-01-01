<?php

namespace Modules\Newsletter\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Core\Tenant\Tenant;
use App\Core\Features\TenantFeature;
use Modules\Newsletter\Entities\NewsletterList;
use Modules\Newsletter\Entities\NewsletterTemplate;
use Modules\Newsletter\Entities\NewsletterAutomation;

class NewsletterDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $tenantId = 1;
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->command->info('Tenant 1 not found. Skipping Newsletter demo data.');
            return;
        }

        $adminUserId = 1; // Assuming user ID 1 is the admin/creator

        $this->command->info('Seeding Newsletter demo data for Tenant 1...');

        // 1. Activate Module
        TenantFeature::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'feature_slug' => 'newsletter',
            ],
            [
                'enabled' => true,
                'limit_value' => 10000, // Example limit
                'source' => 'system',
                'granted_at' => now(),
            ]
        );

        // 2. Lists
        NewsletterList::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => 'all-members',
            ],
            [
                'name' => 'Tutti i soci',
                'description' => 'Lista dinamica di tutti i soci attivi',
                'type' => 'all_members',
                'is_default' => true,
                'is_active' => true,
                'created_by' => $adminUserId,
            ]
        );

        NewsletterList::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => 'general-newsletter',
            ],
            [
                'name' => 'Newsletter Generale',
                'description' => 'Iscrizioni manuali dal sito web',
                'type' => 'manual',
                'is_default' => false,
                'is_active' => true,
                'created_by' => $adminUserId,
            ]
        );

        // 3. Templates
        NewsletterTemplate::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => 'standard-email',
            ],
            [
                'name' => 'Email Standard',
                'type' => 'html',
                'category' => 'General',
                'html_content' => '<html><body><h1>Titolo</h1><p>Contenuto standard...</p></body></html>',
                'is_active' => true,
                'created_by' => $adminUserId,
            ]
        );

        NewsletterTemplate::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => 'event-announcement',
            ],
            [
                'name' => 'Annuncio Evento',
                'type' => 'html',
                'category' => 'Events',
                'html_content' => '<html><body><h1>Nuovo Evento!</h1><p>Non mancare...</p></body></html>',
                'is_active' => true,
                'created_by' => $adminUserId,
            ]
        );

        // 4. Automations
        NewsletterAutomation::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => 'welcome-series',
            ],
            [
                'name' => 'Benvenuto',
                'description' => 'Serie di benvenuto per nuovi iscritti',
                'trigger_type' => 'subscription',
                'is_active' => true,
                'created_by' => $adminUserId,
            ]
        );

        NewsletterAutomation::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => 'membership-expiry',
            ],
            [
                'name' => 'Scadenza Quota',
                'description' => 'Reminder scadenza quota associativa',
                'trigger_type' => 'membership_expiry',
                'is_active' => true,
                'created_by' => $adminUserId,
            ]
        );

        $this->command->info('Newsletter demo data seeded successfully.');
    }
}
