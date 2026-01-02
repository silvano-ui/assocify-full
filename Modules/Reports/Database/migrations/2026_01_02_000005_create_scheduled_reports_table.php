<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('report_template_id')->constrained('report_templates')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('frequency');
            $table->time('schedule_time')->default('08:00');
            $table->tinyInteger('schedule_day')->nullable();
            $table->json('parameters')->nullable();
            $table->json('filters')->nullable();
            $table->json('export_formats');
            $table->json('recipients');
            $table->string('delivery_channel')->default('email');
            $table->string('webhook_url')->nullable();
            $table->string('slack_channel')->nullable();
            $table->json('trigger_conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->text('last_error')->nullable();
            $table->integer('run_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->timestamps();
        });

        // Add FK to generated_reports table for scheduled_report_id now that table exists
        Schema::table('generated_reports', function (Blueprint $table) {
             $table->foreign('scheduled_report_id')->references('id')->on('scheduled_reports')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('generated_reports', function (Blueprint $table) {
            $table->dropForeign(['scheduled_report_id']);
        });
        Schema::dropIfExists('scheduled_reports');
    }
};
