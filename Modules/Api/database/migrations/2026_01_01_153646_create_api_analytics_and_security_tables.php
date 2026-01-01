<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // api_analytics
        Schema::create('api_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->date('date');
            $table->integer('hour')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('method')->nullable();
            $table->integer('total_requests')->default(0);
            $table->integer('successful_requests')->default(0);
            $table->integer('failed_requests')->default(0);
            $table->decimal('avg_response_time_ms', 10, 2)->default(0);
            $table->integer('min_response_time_ms')->nullable();
            $table->integer('max_response_time_ms')->nullable();
            $table->bigInteger('total_bytes_in')->default(0);
            $table->bigInteger('total_bytes_out')->default(0);
            $table->integer('unique_ips')->default(0);
            $table->integer('error_4xx_count')->default(0);
            $table->integer('error_5xx_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'api_key_id', 'date', 'hour', 'endpoint', 'method'], 'api_analytics_unique_idx');
        });

        // api_sandbox_data
        Schema::create('api_sandbox_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->string('resource_type');
            $table->string('resource_id');
            $table->json('data');
            $table->timestamps();

            $table->unique(['api_key_id', 'resource_type', 'resource_id']);
        });

        // api_sdks
        Schema::create('api_sdks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('language', ['javascript', 'typescript', 'php', 'python', 'ruby', 'go', 'java', 'csharp', 'swift', 'kotlin']);
            $table->string('version');
            $table->string('download_url')->nullable();
            $table->string('package_name')->nullable();
            $table->string('repository_url')->nullable();
            $table->string('documentation_url')->nullable();
            $table->boolean('is_official')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('downloads_count')->default(0);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        // api_console_sessions
        Schema::create('api_console_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->string('session_token')->unique();
            $table->string('name')->nullable();
            $table->json('saved_requests')->nullable();
            $table->json('environment_vars')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // api_console_history
        Schema::create('api_console_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('api_console_sessions')->cascadeOnDelete();
            $table->string('method');
            $table->string('endpoint');
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->json('response_headers')->nullable();
            $table->json('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->timestamp('executed_at');
            $table->timestamp('created_at')->useCurrent();
        });

        // api_ip_whitelist
        Schema::create('api_ip_whitelist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->string('ip_address');
            $table->string('cidr_range')->nullable();
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'ip_address']);
        });

        // api_security_events
        Schema::create('api_security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->enum('event_type', ['auth_failure', 'rate_limit_exceeded', 'ip_blocked', 'suspicious_activity', 'token_revoked', 'permission_denied']);
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('endpoint')->nullable();
            $table->json('details')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'created_at']);
        });

        // api_alerts
        Schema::create('api_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['error_rate', 'response_time', 'rate_limit', 'downtime', 'security']);
            $table->json('conditions');
            $table->json('notification_channels');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('trigger_count')->default(0);
            $table->integer('cooldown_minutes')->default(15);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // api_alert_notifications
        Schema::create('api_alert_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('api_alerts')->cascadeOnDelete();
            $table->enum('channel', ['email', 'sms', 'slack', 'webhook', 'in_app']);
            $table->string('recipient');
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed']);
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // api_versions
        Schema::create('api_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('version');
            $table->enum('status', ['current', 'deprecated', 'sunset']);
            $table->text('description')->nullable();
            $table->text('changelog')->nullable();
            $table->json('breaking_changes')->nullable();
            $table->date('deprecation_date')->nullable();
            $table->date('sunset_date')->nullable();
            $table->text('migration_guide')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'version']);
        });

        // api_endpoint_configs
        Schema::create('api_endpoint_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('endpoint_pattern');
            $table->string('method')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('rate_limit_override')->nullable();
            $table->integer('cache_ttl_seconds')->nullable();
            $table->boolean('require_auth')->default(true);
            $table->json('allowed_scopes')->nullable();
            $table->json('custom_headers')->nullable();
            $table->json('transform_request')->nullable();
            $table->json('transform_response')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['tenant_id', 'endpoint_pattern', 'method'], 'api_endpoint_configs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_endpoint_configs');
        Schema::dropIfExists('api_versions');
        Schema::dropIfExists('api_alert_notifications');
        Schema::dropIfExists('api_alerts');
        Schema::dropIfExists('api_security_events');
        Schema::dropIfExists('api_ip_whitelist');
        Schema::dropIfExists('api_console_history');
        Schema::dropIfExists('api_console_sessions');
        Schema::dropIfExists('api_sdks');
        Schema::dropIfExists('api_sandbox_data');
        Schema::dropIfExists('api_analytics');
    }
};
