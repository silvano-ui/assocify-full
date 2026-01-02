<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('report_template_id')->constrained('report_templates')->onDelete('cascade');
            $table->string('name');
            $table->string('period');
            $table->string('period_type');
            $table->json('snapshot_data');
            $table->json('summary')->nullable();
            $table->integer('row_count')->nullable();
            $table->timestamp('snapshot_at');
            $table->timestamps();

            $table->unique(['tenant_id', 'report_template_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_snapshots');
    }
};
