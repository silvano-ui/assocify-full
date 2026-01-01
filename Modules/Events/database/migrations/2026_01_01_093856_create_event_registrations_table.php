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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'attended'])->default('pending');
            $table->integer('guests')->default(0);
            $table->decimal('amount', 8, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('attended_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
