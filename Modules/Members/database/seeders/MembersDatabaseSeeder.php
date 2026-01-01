<?php

namespace Modules\Members\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Core\Tenant\Tenant;
use App\Core\Tenant\TenantModule;
use Modules\Members\Entities\MemberCategory;
use Modules\Members\Entities\MemberProfile;
use Modules\Members\Entities\MemberCard;
use Modules\Members\Entities\MemberCategoryAssignment;
use App\Core\Users\User;
use Illuminate\Support\Facades\Hash;

class MembersDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $tenant = Tenant::where('slug', 'asd-sport-club')->first();

        if (!$tenant) {
            $this->command->error('Tenant ASD Sport Club not found. Please run the main DatabaseSeeder first.');
            return;
        }

        // Enable Members module for this tenant
        TenantModule::updateOrCreate(
            ['tenant_id' => $tenant->id, 'module_slug' => 'members'],
            ['enabled' => true, 'enabled_at' => now()]
        );

        // Create Categories
        $categories = [
            ['name' => 'Junior', 'annual_fee' => 10.00, 'age_max' => 18, 'color' => '#3498db'],
            ['name' => 'Senior', 'annual_fee' => 20.00, 'age_min' => 18, 'age_max' => 65, 'color' => '#2ecc71'],
            ['name' => 'Agonista', 'annual_fee' => 50.00, 'color' => '#e74c3c'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = MemberCategory::create(array_merge($cat, [
                'tenant_id' => $tenant->id,
                'is_active' => true,
                'is_default' => false,
            ]));
        }

        // Create 10 Demo Members
        for ($i = 1; $i <= 10; $i++) {
            // Create User
            $user = User::create([
                'name' => "Member $i",
                'email' => "member{$i}@asdsportclub.it",
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]);

            // Create Profile
            $profile = MemberProfile::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'member_number' => "MEM-" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'birth_date' => now()->subYears(rand(10, 70)),
                'fiscal_code' => "FCODE" . str_pad($i, 11, '0', STR_PAD_LEFT),
                'city' => 'Rome',
                'country' => 'IT',
                'document_type' => 'id_card',
            ]);

            // Assign Category
            $categoryName = array_rand($categoryModels);
            $category = $categoryModels[$categoryName];
            
            MemberCategoryAssignment::create([
                'member_profile_id' => $profile->id,
                'member_category_id' => $category->id,
                'assigned_at' => now(),
                'expires_at' => now()->addYear(),
            ]);

            // Create Card (QR code generated automatically by boot method)
            MemberCard::create([
                'member_profile_id' => $profile->id,
                'status' => 'active',
                'issued_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
        }
    }
}
