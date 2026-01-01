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
        // Newsletter Lists
        Schema::create('newsletter_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['manual', 'dynamic', 'all_members'])->default('manual');
            $table->json('dynamic_filters')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('subscribers_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // Newsletter Subscribers
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('list_id')->constrained('newsletter_lists')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email');
            $table->string('name')->nullable();
            $table->enum('status', ['subscribed', 'unsubscribed', 'bounced', 'complained'])->default('subscribed');
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('bounce_type')->nullable();
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_at')->nullable();
            $table->string('consent_ip')->nullable();
            $table->enum('source', ['manual', 'import', 'registration', 'event', 'api'])->default('manual');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['list_id', 'email']);
            $table->index(['tenant_id', 'email']);
        });

        // Newsletter Templates
        Schema::create('newsletter_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['drag_drop', 'html', 'mjml'])->default('html');
            $table->string('category')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->longText('html_content')->nullable();
            $table->longText('mjml_content')->nullable();
            $table->json('json_design')->nullable();
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // Newsletter Campaigns
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('subject');
            $table->string('preview_text')->nullable();
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('newsletter_templates')->onDelete('set null');
            $table->longText('html_content');
            $table->text('text_content')->nullable();
            $table->json('list_ids'); // Array of list IDs
            $table->json('segment_filters')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            $table->timestamp('send_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletter_campaigns');
        Schema::dropIfExists('newsletter_templates');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('newsletter_lists');
    }
};
