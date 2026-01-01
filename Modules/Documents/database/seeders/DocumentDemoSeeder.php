<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Documents\Entities\DocumentCategory;
use Modules\Documents\Entities\DocumentTemplate;
use App\Core\Users\User;
use App\Core\Tenant\Tenant;
use Illuminate\Support\Str;

class DocumentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Skipping DocumentDemoSeeder.');
            return;
        }

        $tenantId = $user->tenant_id;

        if (!$tenantId) {
            $tenant = Tenant::first();
            if (!$tenant) {
                $tenant = Tenant::create([
                    'name' => 'Demo Tenant',
                    'slug' => 'demo-tenant',
                    'is_active' => true,
                ]);
            }
            $tenantId = $tenant->id;
        }

        // Categories
        $categories = [
            'Regolamenti',
            'Modulistica',
            'Verbali',
            'Certificati'
        ];

        foreach ($categories as $categoryName) {
            DocumentCategory::firstOrCreate(
                [
                    'slug' => Str::slug($categoryName),
                    'tenant_id' => $tenantId
                ],
                [
                    'name' => $categoryName,
                    'is_active' => true,
                    'created_by' => $user->id,
                ]
            );
        }

        // Templates
        $certificatiCategory = DocumentCategory::where('slug', 'certificati')->where('tenant_id', $tenantId)->first();
        
        if ($certificatiCategory) {
            // Tessera Socio
            DocumentTemplate::firstOrCreate(
                [
                    'slug' => 'tessera-socio',
                    'tenant_id' => $tenantId
                ],
                [
                    'name' => 'Tessera Socio',
                    'category_id' => $certificatiCategory->id,
                    'type' => 'membership_card',
                    'description' => 'Template per la tessera socio annuale',
                    'html_content' => '<div class="card"><h1>Tessera Socio</h1><p>Nome: {{ member.name }}</p></div>',
                    'output_format' => 'pdf',
                    'is_active' => true,
                    'created_by' => $user->id,
                ]
            );

            // Ricevuta Quota
            DocumentTemplate::firstOrCreate(
                [
                    'slug' => 'ricevuta-quota',
                    'tenant_id' => $tenantId
                ],
                [
                    'name' => 'Ricevuta Quota',
                    'category_id' => $certificatiCategory->id,
                    'type' => 'receipt',
                    'description' => 'Ricevuta di pagamento quota associativa',
                    'html_content' => '<div class="receipt"><h1>Ricevuta</h1><p>Importo: {{ payment.amount }}</p></div>',
                    'output_format' => 'pdf',
                    'is_active' => true,
                    'created_by' => $user->id,
                ]
            );
        }
    }
}
