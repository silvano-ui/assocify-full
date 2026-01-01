<?php

namespace Database\Seeders;

use App\Core\Features\Feature;
use App\Core\Features\FeatureBundle;
use App\Core\Features\FeatureDependency;
use App\Core\Features\PlanFeature;
use App\Core\Plans\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Features
        $features = [
            // Membership
            [
                'slug' => 'members_limit',
                'name' => 'Member Limit',
                'module' => 'membership',
                'category' => 'Limits',
                'description' => 'Maximum number of members allowed.',
                'unit_name' => 'members',
                'is_active' => true,
                'is_premium' => false,
            ],
            [
                'slug' => 'member_categories',
                'name' => 'Member Categories',
                'module' => 'membership',
                'category' => 'Management',
                'description' => 'Create custom categories for members.',
                'is_active' => true,
                'is_premium' => false,
            ],
            // Events
            [
                'slug' => 'events_basic',
                'name' => 'Basic Events',
                'module' => 'events',
                'category' => 'Events',
                'description' => 'Create and manage simple events.',
                'is_active' => true,
                'is_premium' => false,
            ],
            [
                'slug' => 'events_advanced',
                'name' => 'Advanced Events',
                'module' => 'events',
                'category' => 'Events',
                'description' => 'Ticketing, recurring events, and more.',
                'is_active' => true,
                'is_premium' => true,
                'price_monthly' => 10.00,
            ],
            // Finance
            [
                'slug' => 'invoices',
                'name' => 'Invoicing',
                'module' => 'finance',
                'category' => 'Finance',
                'description' => 'Generate and manage invoices.',
                'is_active' => true,
                'is_premium' => true,
                'price_monthly' => 15.00,
            ],
            [
                'slug' => 'payments_online',
                'name' => 'Online Payments',
                'module' => 'finance',
                'category' => 'Finance',
                'description' => 'Accept online payments via Stripe/PayPal.',
                'is_active' => true,
                'is_premium' => true,
                'price_monthly' => 20.00,
            ],
             // Gallery
            [
                'slug' => 'gallery_storage',
                'name' => 'Gallery Storage',
                'module' => 'gallery',
                'category' => 'Storage',
                'description' => 'Storage space for gallery images.',
                'unit_name' => 'GB',
                'is_active' => true,
                'is_premium' => false,
            ],
             [
                'slug' => 'watermark',
                'name' => 'Watermarking',
                'module' => 'gallery',
                'category' => 'Tools',
                'description' => 'Apply watermarks to images.',
                'is_active' => true,
                'is_premium' => true,
            ],
        ];

        foreach ($features as $data) {
            Feature::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // 2. Dependencies
        $advancedEvents = Feature::where('slug', 'events_advanced')->first();
        $basicEvents = Feature::where('slug', 'events_basic')->first();

        if ($advancedEvents && $basicEvents) {
            FeatureDependency::firstOrCreate([
                 'feature_slug' => $advancedEvents->slug,
                 'requires_feature_slug' => $basicEvents->slug,
            ]);
        }
        
        // 3. Bundles
        $bundle = FeatureBundle::updateOrCreate(
            ['slug' => 'event_power_pack'],
            [
                'name' => 'Event Power Pack',
                'description' => 'All event features + Invoicing for selling tickets.',
                'price_monthly' => 20.00,
                'price_yearly' => 200.00,
                'discount_percent' => 20,
                'is_active' => true,
            ]
        );
        
        $bundleFeatures = Feature::whereIn('slug', ['events_advanced', 'invoices'])->get();
        // Sync using attach/sync on relationship
        if ($bundleFeatures->isNotEmpty()) {
             $bundle->features()->sync($bundleFeatures->pluck('slug')); 
        }

        // 4. Plan Assignments
        $plans = [
            'basic' => ['name' => 'Basic', 'price_monthly' => 0, 'price_yearly' => 0],
            'standard' => ['name' => 'Standard', 'price_monthly' => 29, 'price_yearly' => 290],
            'premium' => ['name' => 'Premium', 'price_monthly' => 99, 'price_yearly' => 990],
        ];
        
        foreach ($plans as $slug => $p) {
            $plan = Plan::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $p['name'], 
                    'price_monthly' => $p['price_monthly'], 
                    'price_yearly' => $p['price_yearly'],
                    'is_active' => true
                ]
            );
            
            // Assign features via PlanFeature
            $featuresToAssign = [];
            if ($slug === 'basic') {
                 $featuresToAssign = [
                     'members_limit' => ['limit_value' => 50, 'limit_type' => 'count', 'included' => true],
                     'events_basic' => ['included' => true],
                     'gallery_storage' => ['limit_value' => 1, 'limit_type' => 'storage_mb', 'included' => true],
                 ];
            } elseif ($slug === 'standard') {
                 $featuresToAssign = [
                     'members_limit' => ['limit_value' => 500, 'limit_type' => 'count', 'included' => true],
                     'events_basic' => ['included' => true],
                     'invoices' => ['included' => true],
                     'gallery_storage' => ['limit_value' => 10, 'limit_type' => 'storage_mb', 'included' => true],
                 ];
            } elseif ($slug === 'premium') {
                 $featuresToAssign = [
                     'members_limit' => ['limit_value' => 0, 'limit_type' => 'unlimited', 'included' => true],
                     'events_basic' => ['included' => true],
                     'events_advanced' => ['included' => true],
                     'invoices' => ['included' => true],
                     'payments_online' => ['included' => true],
                     'watermark' => ['included' => true],
                     'gallery_storage' => ['limit_value' => 100, 'limit_type' => 'storage_mb', 'included' => true],
                 ];
            }
            
            foreach ($featuresToAssign as $fSlug => $pivotData) {
                PlanFeature::updateOrCreate(
                    ['plan_id' => $plan->id, 'feature_slug' => $fSlug],
                    $pivotData
                );
            }
        }
    }
}
