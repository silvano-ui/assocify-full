<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_template_id')->constrained('report_templates')->onDelete('cascade');
            $table->string('name');
            $table->string('field');
            $table->string('type')->default('string');
            $table->string('source')->default('field');
            $table->string('relation_path')->nullable();
            $table->text('formula')->nullable();
            $table->string('aggregation')->nullable();
            $table->string('format')->nullable();
            $table->json('conditional_formatting')->nullable();
            $table->integer('width')->nullable();
            $table->string('align')->default('left');
            $table->boolean('sortable')->default(true);
            $table->boolean('filterable')->default(true);
            $table->boolean('visible')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_columns');
    }
};
