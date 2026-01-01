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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['pdf_fillable', 'html_to_pdf', 'docx_merge', 'certificate', 'receipt', 'membership_card']);
            $table->string('base_file_path')->nullable();
            $table->longText('html_content')->nullable();
            $table->text('css_styles')->nullable();
            $table->json('available_variables')->nullable();
            $table->json('default_values')->nullable();
            $table->enum('output_format', ['pdf', 'docx', 'png'])->default('pdf');
            $table->string('page_size')->default('A4');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->json('margins')->nullable();
            $table->text('header_html')->nullable();
            $table->text('footer_html')->nullable();
            $table->string('watermark_text')->nullable();
            $table->string('watermark_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('document_generated', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('document_templates')->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('member_profiles')->nullOnDelete();
            $table->json('variables_used');
            $table->string('file_path');
            $table->string('original_name');
            $table->bigInteger('size');
            $table->timestamp('generated_at');
            $table->timestamp('expires_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action', ['view', 'download', 'print', 'share', 'sign', 'acknowledge', 'email_sent', 'email_opened']);
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['document_id', 'created_at']);
        });

        Schema::create('document_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->cascadeOnDelete();
            $table->foreignId('signature_request_id')->nullable()->constrained('document_signature_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reminder_type', ['expiry', 'signature_pending', 'acknowledgment_pending', 'review_pending']);
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['scheduled', 'sent', 'failed', 'cancelled']);
            $table->enum('channel', ['email', 'sms', 'push', 'in_app']);
            $table->text('message')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('document_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('shared_by')->constrained('users');
            $table->enum('share_type', ['link', 'email', 'qr']);
            $table->string('token')->unique();
            $table->string('password')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->boolean('allow_download')->default(false);
            $table->boolean('allow_print')->default(false);
            $table->integer('max_views')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->json('accessed_ips')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_shares');
        Schema::dropIfExists('document_reminders');
        Schema::dropIfExists('document_access_logs');
        Schema::dropIfExists('document_generated');
        Schema::dropIfExists('document_templates');
    }
};
