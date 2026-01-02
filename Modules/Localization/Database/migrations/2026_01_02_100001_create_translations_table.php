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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->index();
            $table->string('group')->index(); // messages, validation, auth, etc
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->boolean('is_auto_translated')->default(false);
            $table->string('auto_translation_provider')->nullable(); // deepl/libretranslate
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // FK users
            $table->timestamps();

            $table->unique(['locale', 'group', 'key']);
            
            // $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
};
