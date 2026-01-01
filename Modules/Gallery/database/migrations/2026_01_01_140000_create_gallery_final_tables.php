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
        // 1. collections
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('cover_media_id')->nullable();
            $table->boolean('is_public')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // 2. collection_media
        Schema::create('collection_media', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);

            $table->primary(['collection_id', 'media_id']);
        });

        // 3. slideshows
        Schema::create('slideshows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('album_id')->nullable()->constrained('albums')->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->string('name');
            $table->integer('duration_per_slide')->default(5);
            $table->string('transition_type'); // enum: fade,slide,zoom,ken_burns,none
            $table->boolean('auto_play')->default(true);
            $table->boolean('loop')->default(true);
            $table->boolean('show_captions')->default(true);
            $table->string('background_music_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. download_requests
        Schema::create('download_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('album_id')->nullable()->constrained('albums')->nullOnDelete();
            $table->json('media_ids')->nullable();
            $table->string('zip_path')->nullable();
            $table->string('status'); // enum: pending,processing,ready,expired,failed
            $table->bigInteger('file_size')->nullable();
            $table->string('download_token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 5. watermark_settings
        Schema::create('watermark_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('type'); // enum: text,image
            $table->string('text')->nullable();
            $table->string('image_path')->nullable();
            $table->string('position'); // enum: top-left,top-center,top-right,center,bottom-left,bottom-center,bottom-right,tile
            $table->integer('opacity')->default(50);
            $table->integer('size')->default(20);
            $table->timestamps();

            $table->unique('tenant_id');
        });

        // 6. video_processing_jobs
        Schema::create('video_processing_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('job_type'); // enum: transcode,thumbnail,gif_preview,hls
            $table->string('status'); // enum: pending,processing,completed,failed
            $table->integer('progress')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_processing_jobs');
        Schema::dropIfExists('watermark_settings');
        Schema::dropIfExists('download_requests');
        Schema::dropIfExists('slideshows');
        Schema::dropIfExists('collection_media');
        Schema::dropIfExists('collections');
    }
};
