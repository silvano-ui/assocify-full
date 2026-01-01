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
        // 1. custom_groups
        Schema::create('custom_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // 2. custom_group_members
        Schema::create('custom_group_members', function (Blueprint $table) {
            $table->foreignId('custom_group_id')->constrained('custom_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('added_at')->useCurrent();

            $table->primary(['custom_group_id', 'user_id']);
        });

        // 3. albums
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('albums')->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('cover_media_id')->nullable();
            $table->string('visibility'); // enum: public,members,participants,private,link_only
            $table->boolean('is_collaborative')->default(false);
            $table->string('password')->nullable();
            $table->boolean('download_enabled')->default(true);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_likes')->default(true);
            $table->boolean('allow_external_share')->default(true);
            $table->string('share_token')->nullable()->unique();
            $table->timestamp('share_expires_at')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('views_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        // 4. album_access
        Schema::create('album_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete();
            $table->string('access_type'); // enum: user,event_participant,member_category,role,custom_group
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->foreignId('member_category_id')->nullable()->constrained('member_categories')->cascadeOnDelete();
            $table->string('role_name')->nullable();
            $table->foreignId('custom_group_id')->nullable()->constrained('custom_groups')->cascadeOnDelete();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_upload')->default(false);
            $table->boolean('can_download')->default(true);
            $table->boolean('can_share')->default(false);
            $table->foreignId('granted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 5. album_collaborators
        Schema::create('album_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role'); // enum: viewer,contributor,editor,admin
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['album_id', 'user_id']);
        });

        // 6. album_share_links
        Schema::create('album_share_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('name')->nullable();
            $table->string('password')->nullable();
            $table->boolean('allow_download')->default(false);
            $table->integer('max_views')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_share_links');
        Schema::dropIfExists('album_collaborators');
        Schema::dropIfExists('album_access');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('custom_group_members');
        Schema::dropIfExists('custom_groups');
    }
};
