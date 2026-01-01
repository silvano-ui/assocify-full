<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Newsletter Campaign Stats
        Schema::create('newsletter_campaign_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->unique()->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->integer('sent')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('opened')->default(0);
            $table->integer('unique_opens')->default(0);
            $table->integer('clicked')->default(0);
            $table->integer('unique_clicks')->default(0);
            $table->integer('bounced')->default(0);
            $table->integer('soft_bounced')->default(0);
            $table->integer('hard_bounced')->default(0);
            $table->integer('complained')->default(0);
            $table->integer('unsubscribed')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });

        // Newsletter Tracking
        Schema::create('newsletter_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->onDelete('cascade');
            $table->string('email');
            $table->enum('event_type', ['sent', 'delivered', 'opened', 'clicked', 'bounced', 'complained', 'unsubscribed']);
            $table->text('link_url')->nullable();
            $table->integer('link_index')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet', 'unknown'])->nullable();
            $table->string('email_client')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['campaign_id', 'event_type']);
        });

        // Newsletter Links
        Schema::create('newsletter_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->text('original_url');
            $table->string('tracking_url')->unique();
            $table->integer('click_count')->default(0);
            $table->integer('unique_clicks')->default(0);
            $table->integer('position')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Newsletter A/B Tests
        Schema::create('newsletter_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->enum('test_type', ['subject', 'content', 'from_name', 'send_time']);
            $table->json('variant_a');
            $table->json('variant_b');
            $table->enum('winner_criteria', ['open_rate', 'click_rate', 'manual']);
            $table->integer('test_size_percent')->default(20);
            $table->integer('test_duration_hours')->default(4);
            $table->enum('status', ['pending', 'running', 'completed', 'cancelled']);
            $table->enum('winner', ['a', 'b', 'tie'])->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Newsletter Automations
        Schema::create('newsletter_automations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['subscription', 'birthday', 'event_registration', 'event_reminder', 'membership_expiry', 'membership_renewed', 'welcome', 'custom']);
            $table->json('trigger_config')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('total_sent')->default(0);
            $table->integer('total_opened')->default(0);
            $table->integer('total_clicked')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // Newsletter Automation Steps
        Schema::create('newsletter_automation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained('newsletter_automations')->onDelete('cascade');
            $table->integer('step_order');
            $table->enum('action_type', ['send_email', 'wait', 'condition', 'update_subscriber']);
            $table->foreignId('template_id')->nullable()->constrained('newsletter_templates')->onDelete('set null');
            $table->string('email_subject')->nullable();
            $table->integer('wait_duration')->nullable();
            $table->enum('wait_unit', ['minutes', 'hours', 'days'])->nullable();
            $table->string('condition_type')->nullable();
            $table->json('condition_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sent_count')->default(0);
            $table->timestamps();
        });

        // Newsletter Automation Logs
        Schema::create('newsletter_automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained('newsletter_automations')->onDelete('cascade');
            $table->foreignId('step_id')->constrained('newsletter_automation_steps')->onDelete('cascade');
            $table->foreignId('subscriber_id')->nullable()->constrained('newsletter_subscribers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email');
            $table->enum('status', ['pending', 'sent', 'skipped', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Newsletter Queue
        Schema::create('newsletter_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->foreignId('automation_id')->nullable()->constrained('newsletter_automations')->onDelete('cascade');
            $table->foreignId('subscriber_id')->nullable()->constrained('newsletter_subscribers')->onDelete('cascade');
            $table->string('email');
            $table->string('subject');
            $table->longText('html_content');
            $table->text('text_content')->nullable();
            $table->string('from_name');
            $table->string('from_email');
            $table->integer('priority')->default(5);
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->enum('status', ['pending', 'processing', 'sent', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletter_queue');
        Schema::dropIfExists('newsletter_automation_logs');
        Schema::dropIfExists('newsletter_automation_steps');
        Schema::dropIfExists('newsletter_automations');
        Schema::dropIfExists('newsletter_ab_tests');
        Schema::dropIfExists('newsletter_links');
        Schema::dropIfExists('newsletter_tracking');
        Schema::dropIfExists('newsletter_campaign_stats');
    }
};
