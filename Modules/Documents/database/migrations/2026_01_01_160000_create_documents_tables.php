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
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('disk')->default('local');
            $table->bigInteger('size')->nullable();
            $table->string('mime')->nullable();
            $table->string('extension')->nullable();
            $table->integer('version')->default(1);
            $table->enum('visibility', ['public', 'members', 'private', 'signers_only']);
            $table->boolean('requires_signature')->default(false);
            $table->boolean('requires_acknowledgment')->default(false);
            $table->enum('signature_order', ['any', 'sequential'])->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->integer('expiry_reminder_days')->nullable();
            $table->boolean('is_template')->default(false);
            $table->enum('template_type', ['pdf_form', 'html', 'docx'])->nullable();
            $table->longText('template_content')->nullable();
            $table->json('template_fields')->nullable();
            $table->foreignId('generated_from_template_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->string('verification_code')->nullable()->unique();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->integer('version');
            $table->string('file_path');
            $table->string('original_name');
            $table->bigInteger('size');
            $table->string('mime');
            $table->text('changes_summary')->nullable();
            $table->boolean('is_current')->default(false);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('document_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->enum('access_type', ['user', 'member_category', 'role', 'all_members']);
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_category_id')->nullable()->constrained('member_categories')->cascadeOnDelete();
            $table->string('role_name')->nullable();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_download')->default(true);
            $table->boolean('can_sign')->default(false);
            $table->foreignId('granted_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_access');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
    }
};
