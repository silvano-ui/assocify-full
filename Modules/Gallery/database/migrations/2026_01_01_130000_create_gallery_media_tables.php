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
        // 1. media
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('album_id')->nullable()->constrained('albums')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // enum: image,video,audio,panorama_360,drone
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('original_name');
            $table->bigInteger('size');
            $table->string('mime');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('bitrate')->nullable();
            $table->float('fps')->nullable();
            $table->string('codec')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('webp_path')->nullable();
            $table->string('gif_preview_path')->nullable();
            $table->string('watermarked_path')->nullable();
            $table->json('metadata')->nullable();
            $table->json('exif_data')->nullable();
            $table->text('caption')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. media_variants
        Schema::create('media_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('variant_type'); // enum: thumbnail,small,medium,large,hd,fullhd,4k,webp
            $table->string('disk');
            $table->string('path');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->bigInteger('size');
            $table->string('mime');
            $table->timestamps();
        });

        // 3. media_tags
        Schema::create('media_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('color')->nullable();
            $table->integer('media_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // 4. media_tag_pivot
        Schema::create('media_tag_pivot', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('media_tags')->cascadeOnDelete();
            $table->primary(['media_id', 'tag_id']);
        });

        // 5. media_people_tags
        Schema::create('media_people_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->float('position_x');
            $table->float('position_y');
            $table->float('width');
            $table->float('height');
            $table->foreignId('tagged_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 6. media_locations
        Schema::create('media_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('altitude')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->bigInteger('gpx_track_id')->nullable();
            $table->timestamps();
        });

        // 7. media_likes
        Schema::create('media_likes', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['media_id', 'user_id']);
        });

        // 8. media_comments
        Schema::create('media_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('media_comments')->nullOnDelete();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_comments');
        Schema::dropIfExists('media_likes');
        Schema::dropIfExists('media_locations');
        Schema::dropIfExists('media_people_tags');
        Schema::dropIfExists('media_tag_pivot');
        Schema::dropIfExists('media_tags');
        Schema::dropIfExists('media_variants');
        Schema::dropIfExists('media');
    }
};
