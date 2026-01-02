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
        Schema::create('translation_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // null = piattaforma
            $table->string('provider'); // deepl/libretranslate/manual
            $table->text('api_key')->nullable(); // encrypted
            $table->string('api_url')->nullable(); // per LibreTranslate self-hosted
            $table->boolean('is_active')->default(false);
            $table->integer('monthly_char_limit')->nullable();
            $table->integer('chars_used_this_month')->default(0);
            $table->tinyInteger('reset_day')->default(1);
            $table->timestamps();

            // $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->unique(['tenant_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translation_settings');
    }
};
