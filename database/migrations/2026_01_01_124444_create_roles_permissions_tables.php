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
        // 1. permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('module'); // e.g., 'members', 'events'
            $table->string('category')->nullable(); // e.g., 'view', 'edit'
            $table->boolean('is_system')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. tenant_roles
        Schema::create('tenant_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // e.g., 'danger', 'success'
            $table->string('icon')->nullable(); // e.g., 'heroicon-o-shield-check'
            $table->boolean('is_default')->default(false); // Assigned to new members by default
            $table->boolean('is_system')->default(false); // Cannot be deleted/renamed
            $table->boolean('can_be_deleted')->default(true);
            $table->integer('hierarchy_level')->default(0); // 0 = lowest
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        // 3. tenant_role_permissions
        Schema::create('tenant_role_permissions', function (Blueprint $table) {
            $table->foreignId('tenant_role_id')->constrained('tenant_roles')->cascadeOnDelete();
            $table->string('permission_slug');
            $table->boolean('granted')->default(true);
            $table->timestamps();

            $table->primary(['tenant_role_id', 'permission_slug']);
            // Optional: foreign key for permission_slug if we want strict integrity, 
            // but string reference allows for decoupled permission definitions if needed.
            // Given 'permissions' table exists, we should probably constrain it if possible, 
            // but 'slug' is string. 
            $table->foreign('permission_slug')->references('slug')->on('permissions')->cascadeOnDelete();
        });

        // 4. user_tenant_roles
        Schema::create('user_tenant_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_role_id')->constrained('tenant_roles')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'tenant_id', 'tenant_role_id']);
        });

        // 5. role_templates
        Schema::create('role_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // List of permission slugs
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_templates');
        Schema::dropIfExists('user_tenant_roles');
        Schema::dropIfExists('tenant_role_permissions');
        Schema::dropIfExists('tenant_roles');
        Schema::dropIfExists('permissions');
    }
};
