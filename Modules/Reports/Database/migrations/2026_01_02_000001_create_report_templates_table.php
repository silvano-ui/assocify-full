<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('data_source');
            $table->string('base_model')->nullable();
            $table->json('joins')->nullable();
            $table->json('default_columns')->nullable();
            $table->json('available_columns')->nullable();
            $table->json('default_filters')->nullable();
            $table->json('default_sorting')->nullable();
            $table->json('grouping')->nullable();
            $table->json('aggregations')->nullable();
            $table->json('formulas')->nullable();
            $table->json('conditional_formatting')->nullable();
            $table->json('chart_config')->nullable();
            $table->json('pivot_config')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_export_pdf')->default(true);
            $table->boolean('allow_export_excel')->default(true);
            $table->boolean('allow_export_csv')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
