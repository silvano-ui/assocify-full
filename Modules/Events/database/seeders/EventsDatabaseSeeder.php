<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Core\Tenant\Tenant;
use Modules\Events\Entities\EventCategory;
use Modules\Events\Entities\Event;
use App\Core\Users\User;

class EventsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $tenant = Tenant::first();
        if (!$tenant) {
            return;
        }
        
        // Ensure we have a user to attribute creation
        $user = User::first();
        if (!$user) {
            return;
        }
        
        // Mock authentication for tenant scope
        auth()->login($user);

        // Categories: Allenamento, Gara, Social
        $categories = [
            [
                'name' => 'Allenamento',
                'slug' => 'allenamento',
                'color' => '#3b82f6', // blue
                'icon' => 'heroicon-o-bolt',
            ],
            [
                'name' => 'Gara',
                'slug' => 'gara',
                'color' => '#ef4444', // red
                'icon' => 'heroicon-o-trophy',
            ],
            [
                'name' => 'Social',
                'slug' => 'social',
                'color' => '#10b981', // green
                'icon' => 'heroicon-o-users',
            ],
        ];

        foreach ($categories as $catData) {
            EventCategory::firstOrCreate(
                ['slug' => $catData['slug'], 'tenant_id' => $tenant->id],
                array_merge($catData, ['tenant_id' => $tenant->id])
            );
        }

        // Create 5 demo events
        $catAllenamento = EventCategory::where('slug', 'allenamento')->where('tenant_id', $tenant->id)->first();
        
        if ($catAllenamento) {
            for ($i = 1; $i <= 5; $i++) {
                Event::create([
                    'tenant_id' => $tenant->id,
                    'event_category_id' => $catAllenamento->id,
                    'title' => "Allenamento Demo $i",
                    'slug' => "allenamento-demo-$i",
                    'description' => "Descrizione per allenamento demo $i",
                    'starts_at' => now()->addDays($i),
                    'ends_at' => now()->addDays($i)->addHours(2),
                    'status' => 'published',
                    'created_by' => $user->id,
                    'is_public' => true,
                    'is_free' => true,
                ]);
            }
        }
    }
}
