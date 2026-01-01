<?php

namespace Modules\Api\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Api\Entities\ApiKey;
use Modules\Api\Entities\ApiRequestLog;
use Modules\Api\Entities\ApiWebhook;
use Modules\Api\Entities\ApiSecurityEvent;
use App\Core\Tenant\TenantModule;
use App\Core\Users\User;
use Illuminate\Support\Str;

class ApiDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $tenantId = 1; // Assuming tenant 1 exists
        $userId = User::first()->id ?? 1;

        // Activate API module for tenant 1
        TenantModule::updateOrCreate(
            ['tenant_id' => $tenantId, 'module_slug' => 'api'],
            ['enabled' => true, 'enabled_at' => now(), 'settings' => []]
        );

        // Create Production Key
        $key = ApiKey::create([
            'tenant_id' => $tenantId,
            'name' => 'Production Key',
            'key' => 'pk_live_' . Str::random(32),
            'secret_hash' => Str::random(64),
            'type' => 'live',
            'permissions' => ['read', 'write', 'delete'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 10000,
            'is_active' => true,
            'created_by' => $userId,
        ]);

        // Create Sandbox Key
        ApiKey::create([
            'tenant_id' => $tenantId,
            'name' => 'Sandbox Key',
            'key' => 'pk_test_' . Str::random(32),
            'secret_hash' => Str::random(64),
            'type' => 'sandbox',
            'permissions' => ['read', 'write'],
            'rate_limit_per_minute' => 1000,
            'rate_limit_per_day' => 100000,
            'is_active' => true,
            'created_by' => $userId,
        ]);

        // Create Webhook
        ApiWebhook::create([
            'tenant_id' => $tenantId,
            'name' => 'User Created Webhook',
            'url' => 'https://example.com/webhooks/user-created',
            'secret' => Str::random(32),
            'events' => ['member.created'],
            'is_active' => true,
            'created_by' => $userId,
        ]);

        // Create Logs
        ApiRequestLog::create([
            'tenant_id' => $tenantId,
            'api_key_id' => $key->id,
            'method' => 'GET',
            'endpoint' => 'api/v1/members',
            'response_status' => 200,
            'response_time_ms' => 45,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PostmanRuntime/7.29.0',
            'created_at' => now()->subMinutes(5),
        ]);

        ApiRequestLog::create([
            'tenant_id' => $tenantId,
            'api_key_id' => $key->id,
            'method' => 'POST',
            'endpoint' => 'api/v1/members',
            'response_status' => 201,
            'response_time_ms' => 120,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PostmanRuntime/7.29.0',
            'created_at' => now()->subMinutes(2),
        ]);

        // Create Security Event
        ApiSecurityEvent::create([
            'tenant_id' => $tenantId,
            'event_type' => 'auth_failure',
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Unknown',
            'severity' => 'medium',
            'details' => json_encode(['reason' => 'Invalid API Key']),
            'created_at' => now()->subHour(),
        ]);
    }
}
