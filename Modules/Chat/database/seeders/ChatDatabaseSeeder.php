<?php

namespace Modules\Chat\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Chat\Entities\Conversation;
use Modules\Chat\Entities\Message;
use Modules\Chat\Entities\ChatHashtag;
use App\Core\Users\User;
use App\Core\Tenant\TenantModule;

class ChatDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;

        // Activate Chat module for tenant 1
        TenantModule::updateOrCreate(
            ['tenant_id' => $tenantId, 'module_slug' => 'chat'],
            ['enabled' => true, 'enabled_at' => now(), 'settings' => []]
        );

        $users = User::where('tenant_id', $tenantId)->limit(5)->get();
        if ($users->count() < 2) {
            // Need at least 2 users
            return;
        }

        $creator = $users->first();
        $otherUsers = $users->slice(1);

        // 1. Create Public Channel
        $channel = Conversation::create([
            'tenant_id' => $tenantId,
            'type' => 'channel',
            'name' => 'General',
            'description' => 'General discussion',
            'is_private' => false,
            'created_by' => $creator->id,
            'last_message_at' => now(),
        ]);

        // Add participants
        $channel->participants()->attach($creator->id, ['role' => 'owner']);
        foreach ($otherUsers as $user) {
            $channel->participants()->attach($user->id, ['role' => 'member']);
        }

        // Add messages
        Message::create([
            'conversation_id' => $channel->id,
            'user_id' => $creator->id,
            'body' => 'Welcome to the General channel!',
            'created_at' => now()->subMinutes(10),
        ]);

        foreach ($otherUsers as $user) {
            Message::create([
                'conversation_id' => $channel->id,
                'user_id' => $user->id,
                'body' => 'Hello everyone!',
                'created_at' => now()->subMinutes(rand(1, 9)),
            ]);
        }

        // 2. Create Group Chat
        $group = Conversation::create([
            'tenant_id' => $tenantId,
            'type' => 'group',
            'name' => 'Project Alpha',
            'is_private' => true,
            'created_by' => $creator->id,
            'last_message_at' => now(),
        ]);

        $group->participants()->attach($creator->id, ['role' => 'admin']);
        if ($otherUsers->first()) {
             $group->participants()->attach($otherUsers->first()->id, ['role' => 'member']);
        }

        Message::create([
            'conversation_id' => $group->id,
            'user_id' => $creator->id,
            'body' => 'Let\'s discuss Project Alpha.',
        ]);

        // 3. Create Direct Message
        if ($otherUsers->first()) {
            $dm = Conversation::create([
                'tenant_id' => $tenantId,
                'type' => 'direct',
                'is_private' => true,
                'created_by' => $creator->id,
                'last_message_at' => now(),
            ]);
            
            $dm->participants()->attach($creator->id, ['role' => 'member']);
            $dm->participants()->attach($otherUsers->first()->id, ['role' => 'member']);

            Message::create([
                'conversation_id' => $dm->id,
                'user_id' => $creator->id,
                'body' => 'Hey, quick question.',
            ]);
        }

        // 4. Create Hashtags
        ChatHashtag::create([
            'tenant_id' => $tenantId,
            'tag' => 'welcome',
            'message_count' => 1,
            'last_used_at' => now(),
        ]);
        
        ChatHashtag::create([
            'tenant_id' => $tenantId,
            'tag' => 'urgent',
            'message_count' => 0,
        ]);
    }
}
