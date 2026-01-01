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
        // Branding
        Schema::create('tenant_branding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('logo_path')->nullable();
            $table->string('logo_dark_path')->nullable();
            $table->string('favicon_path')->nullable();
            
            // Colors
            $table->string('primary_color')->default('#3B82F6');
            $table->string('secondary_color')->default('#1E40AF');
            $table->string('accent_color')->default('#F59E0B');
            $table->string('success_color')->default('#10B981');
            $table->string('warning_color')->default('#F59E0B');
            $table->string('danger_color')->default('#EF4444');
            $table->string('background_color')->default('#F3F4F6');
            $table->string('sidebar_color')->default('#1F2937');
            $table->string('text_color')->default('#111827');
            
            // Typography
            $table->string('font_family')->default('Inter');
            $table->string('font_url')->nullable();
            $table->string('heading_font')->nullable();
            
            // Customization
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->enum('theme_mode', ['light', 'dark', 'auto'])->default('auto');
            $table->string('preset_theme')->nullable();
            
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Domains
        Schema::create('tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('domain')->unique();
            $table->enum('type', ['subdomain', 'custom']);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->enum('ssl_status', ['pending', 'active', 'failed', 'expired'])->default('pending');
            $table->timestamp('ssl_expires_at')->nullable();
            $table->json('dns_records')->nullable();
            
            $table->timestamps();
            
            $table->index('domain');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Email Branding
        Schema::create('tenant_email_branding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->longText('email_header_html')->nullable();
            $table->longText('email_footer_html')->nullable();
            $table->text('email_signature')->nullable();
            $table->string('email_logo_path')->nullable();
            $table->string('email_primary_color')->nullable();
            $table->json('email_template_overrides')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Login Branding
        Schema::create('tenant_login_branding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('page_title')->nullable();
            $table->string('welcome_text')->nullable();
            $table->text('welcome_subtext')->nullable();
            $table->string('background_image_path')->nullable();
            $table->string('background_color')->nullable();
            $table->string('background_gradient')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->enum('logo_size', ['small', 'medium', 'large'])->default('medium');
            $table->boolean('show_social_login')->default(true);
            $table->text('custom_css')->nullable();
            $table->boolean('registration_enabled')->default(true);
            $table->text('registration_welcome_text')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // PWA Settings
        Schema::create('tenant_pwa_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('app_name')->nullable();
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            
            $table->string('icon_72')->nullable();
            $table->string('icon_96')->nullable();
            $table->string('icon_128')->nullable();
            $table->string('icon_144')->nullable();
            $table->string('icon_152')->nullable();
            $table->string('icon_192')->nullable();
            $table->string('icon_384')->nullable();
            $table->string('icon_512')->nullable();
            
            $table->string('splash_screen_path')->nullable();
            $table->string('theme_color')->nullable();
            $table->string('background_color')->nullable();
            
            $table->enum('display_mode', ['standalone', 'fullscreen', 'minimal-ui', 'browser'])->default('standalone');
            $table->enum('orientation', ['any', 'portrait', 'landscape'])->default('any');
            $table->string('start_url')->nullable();
            $table->string('scope')->nullable();
            
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // PDF Branding
        Schema::create('tenant_pdf_branding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('header_logo_path')->nullable();
            $table->string('header_text')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('footer_logo_path')->nullable();
            $table->string('watermark_text')->nullable();
            $table->string('watermark_image_path')->nullable();
            $table->integer('watermark_opacity')->default(20);
            $table->string('primary_color')->nullable();
            $table->string('font_family')->nullable();
            $table->enum('paper_size', ['A4', 'Letter', 'Legal'])->default('A4');
            $table->json('margins')->nullable();
            
            $table->string('invoice_template')->nullable();
            $table->string('receipt_template')->nullable();
            $table->string('certificate_template')->nullable();
            
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Menu Settings
        Schema::create('tenant_menu_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->json('hidden_modules')->nullable();
            $table->json('custom_menu_items')->nullable();
            $table->json('menu_order')->nullable();
            $table->json('quick_actions')->nullable();
            $table->json('dashboard_widgets')->nullable();
            
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_menu_settings');
        Schema::dropIfExists('tenant_pdf_branding');
        Schema::dropIfExists('tenant_pwa_settings');
        Schema::dropIfExists('tenant_login_branding');
        Schema::dropIfExists('tenant_email_branding');
        Schema::dropIfExists('tenant_domains');
        Schema::dropIfExists('tenant_branding');
    }
};
