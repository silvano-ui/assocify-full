<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // Nullable, cascade
            $table->string('translatable_type'); // Event, Document, Newsletter
            $table->unsignedBigInteger('translatable_id');
            $table->string('field'); // title, description, content
            $table->string('locale', 10);
            $table->text('value')->nullable();
            $table->boolean('is_auto_translated')->default(false);
            $table->string('auto_translation_provider')->nullable();
            $table->timestamps();

            // $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            $table->index(['translatable_type', 'translatable_id', 'field', 'locale'], 'dynamic_translations_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_translations');
    }
};
