<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_engagement_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            // Using constrained with table name 'member_profiles' assuming it exists from previous context
            // In Modules/Members/Entities/MemberProfile.php the model is MemberProfile.
            // Assuming table name follows convention 'member_profiles'.
            $table->foreignId('member_profile_id')->constrained('member_profiles')->onDelete('cascade');
            $table->integer('score');
            $table->string('segment');
            $table->integer('events_attended_count')->default(0);
            $table->decimal('payments_on_time_rate', 5, 2)->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->integer('churn_risk_score')->nullable();
            $table->json('factors')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['tenant_id', 'member_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_engagement_scores');
    }
};
