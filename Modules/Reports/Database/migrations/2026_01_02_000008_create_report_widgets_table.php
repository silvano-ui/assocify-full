<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('type');
            $table->string('data_source');
            $table->json('query_config')->nullable();
            $table->json('chart_config')->nullable();
            $table->json('display_config')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->tinyInteger('span')->default(1);
            $table->integer('refresh_interval')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_widgets');
    }
};
