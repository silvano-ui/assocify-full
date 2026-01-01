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
        // api_keys
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('secret_hash');
            $table->enum('type', ['live', 'sandbox']);
            $table->json('permissions')->nullable();
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('rate_limit_per_day')->default(10000);
            $table->json('allowed_ips')->nullable();
            $table->json('allowed_domains')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // api_key_scopes
        Schema::create('api_key_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->string('scope');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['api_key_id', 'scope']);
        });

        // api_requests_log
        Schema::create('api_requests_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method');
            $table->string('endpoint');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_status');
            $table->integer('response_time_ms');
            $table->integer('response_size_bytes')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['api_key_id', 'created_at']);
        });

        // api_rate_limits
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->enum('period', ['minute', 'hour', 'day']);
            $table->integer('limit_value');
            $table->integer('current_count')->default(0);
            $table->timestamp('reset_at');
            $table->timestamps();
        });

        // api_webhooks
        Schema::create('api_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('url');
            $table->string('secret');
            $table->json('events');
            $table->json('headers')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('retry_count')->default(3);
            $table->integer('timeout_seconds')->default(30);
            $table->timestamp('last_triggered_at')->nullable();
            $table->enum('last_status', ['success', 'failed'])->nullable();
            $table->integer('failure_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // api_webhook_events
        Schema::create('api_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('api_webhooks')->cascadeOnDelete();
            $table->string('event_type');
            $table->json('payload');
            $table->enum('status', ['pending', 'sent', 'failed', 'retrying']);
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // api_oauth_clients
        Schema::create('api_oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('client_id')->unique();
            $table->string('client_secret_hash');
            $table->json('redirect_uris');
            $table->json('grant_types');
            $table->json('scopes')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // api_oauth_tokens
        Schema::create('api_oauth_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oauth_client_id')->constrained('api_oauth_clients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('access_token_hash')->unique();
            $table->string('refresh_token_hash')->nullable()->unique();
            $table->json('scopes')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('refresh_expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // api_documentation
        Schema::create('api_documentation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('version');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('openapi_spec');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_current')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_documentation');
        Schema::dropIfExists('api_oauth_tokens');
        Schema::dropIfExists('api_oauth_clients');
        Schema::dropIfExists('api_webhook_events');
        Schema::dropIfExists('api_webhooks');
        Schema::dropIfExists('api_rate_limits');
        Schema::dropIfExists('api_requests_log');
        Schema::dropIfExists('api_key_scopes');
        Schema::dropIfExists('api_keys');
    }
};
