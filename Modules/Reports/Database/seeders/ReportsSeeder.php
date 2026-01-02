<?php

namespace Modules\Reports\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Reports\Entities\ReportTemplate;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'name' => 'Members List',
                'slug' => 'members-list',
                'data_source' => 'members',
                'description' => 'List of all members',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Payment Summary',
                'slug' => 'payment-summary',
                'data_source' => 'payments',
                'description' => 'Summary of payments received',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Events Overview',
                'slug' => 'events-overview',
                'data_source' => 'events',
                'description' => 'Overview of past and upcoming events',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Subscription Renewals',
                'slug' => 'subscription-renewals',
                'data_source' => 'subscriptions',
                'description' => 'Upcoming subscription renewals',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Invoices',
                'slug' => 'unpaid-invoices',
                'data_source' => 'invoices',
                'description' => 'List of unpaid invoices',
                'is_system' => true,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            ReportTemplate::firstOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
