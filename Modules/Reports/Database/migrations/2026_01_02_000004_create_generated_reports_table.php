<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('report_template_id')->nullable()->constrained('report_templates')->onDelete('set null');
            // Assuming scheduled_reports table will be created next, we can add the constraint later or assume order.
            // But since migrations run in order, scheduled_reports is created in 000005. 
            // So we can't add foreign key constraint to scheduled_reports yet if we want strict ordering.
            // However, typically in Laravel we can define the column and add constraint later or just define column.
            // Given the instruction order, I will define the column. To be safe with strict mode, I will use bigInteger.
            // If the user wants strict FK immediately, the order matters. 
            // 000005 is scheduled_reports. So generated_reports (000004) cannot have FK to scheduled_reports (000005) immediately.
            // I will add the column as unsignedBigInteger and nullable.
            $table->unsignedBigInteger('scheduled_report_id')->nullable(); 
            
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('type');
            $table->string('format');
            $table->string('status')->default('pending');
            $table->json('parameters')->nullable();
            $table->json('applied_filters')->nullable();
            $table->json('columns_used')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('row_count')->nullable();
            $table->integer('processing_time_ms')->nullable();
            $table->json('summary_data')->nullable();
            $table->json('chart_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
