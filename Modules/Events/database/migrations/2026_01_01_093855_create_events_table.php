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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_category_id')->nullable()->constrained('event_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('registration_starts')->nullable();
            $table->dateTime('registration_ends')->nullable();
            $table->integer('max_participants')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_public')->default(true);
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->string('cover_image')->nullable();
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
