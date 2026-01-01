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
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('signature_type', ['drawn', 'typed', 'otp', 'certificate']);
            $table->longText('signature_data')->nullable();
            $table->string('signature_image_path')->nullable();
            $table->string('typed_name')->nullable();
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->json('certificate_data')->nullable();
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->json('device_info')->nullable();
            $table->string('signature_hash');
            $table->integer('order_position')->default(0);
            $table->enum('status', ['pending', 'signed', 'declined', 'expired']);
            $table->text('declined_reason')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('document_signature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('signer_user_id')->nullable()->constrained('users');
            $table->string('signer_email');
            $table->string('signer_name');
            $table->integer('order_position')->default(1);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'viewed', 'signed', 'declined', 'expired', 'cancelled']);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminder_at')->nullable();
            $table->string('access_token')->unique();
            $table->timestamps();
        });

        Schema::create('document_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('acknowledged_at');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'user_id']);
        });

        Schema::create('document_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('steps');
            $table->boolean('is_template')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('current_step')->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled']);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('document_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('document_workflows')->cascadeOnDelete();
            $table->integer('step_number');
            $table->enum('action_type', ['signature', 'approval', 'acknowledgment', 'review', 'notification']);
            $table->enum('assignee_type', ['user', 'role', 'member_category']);
            $table->integer('assignee_id')->nullable();
            $table->string('assignee_role')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('due_days')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped']);
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_workflow_steps');
        Schema::dropIfExists('document_workflows');
        Schema::dropIfExists('document_acknowledgments');
        Schema::dropIfExists('document_signature_requests');
        Schema::dropIfExists('document_signatures');
    }
};
