<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Core\Plans\Plan;
use App\Core\Tenant\Tenant;
use App\Core\Users\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Plans
        $basePlan = Plan::create([
            'slug' => 'base',
            'name' => 'Base',
            'description' => 'Basic plan for small associations',
            'price_monthly' => 9.00,
            'price_yearly' => 90.00,
            'modules' => [], // Add basic modules later
            'max_members' => 100,
            'features' => ['Basic reporting', 'Member management'],
            'is_active' => true,
        ]);

        $proPlan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'description' => 'Professional plan for growing associations',
            'price_monthly' => 29.00,
            'price_yearly' => 290.00,
            'modules' => [], // Add more modules
            'max_members' => 1000,
            'features' => ['Advanced reporting', 'Member management', 'Email campaigns'],
            'is_active' => true,
        ]);

        $enterprisePlan = Plan::create([
            'slug' => 'enterprise',
            'name' => 'Enterprise',
            'description' => 'Unlimited plan for large associations',
            'price_monthly' => 79.00,
            'price_yearly' => 790.00,
            'modules' => [], // All modules
            'max_members' => 0, // Unlimited
            'features' => ['All features', 'Priority support'],
            'is_active' => true,
        ]);

        // 2. Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@assocify.it',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 3. Create Demo Tenant "ASD Sport Club" with Pro Plan
        $tenant = Tenant::create([
            'name' => 'ASD Sport Club',
            'slug' => 'asd-sport-club',
            'plan_id' => $proPlan->id,
            'is_active' => true,
        ]);

        // 4. Create Tenant Admin
        User::create([
            'name' => 'Tenant Admin',
            'email' => 'admin@asdsportclub.it',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        // 5. Enable Modules for Tenant
        \App\Core\Tenant\TenantModule::create([
            'tenant_id' => $tenant->id,
            'module_slug' => 'members',
            'enabled' => true,
            'enabled_at' => now(),
        ]);

        \App\Core\Tenant\TenantModule::create([
            'tenant_id' => $tenant->id,
            'module_slug' => 'events',
            'enabled' => true,
            'enabled_at' => now(),
        ]);
    }
}
