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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // es: it, en, de, fr, zh-CN
            $table->string('name'); // es: Italiano, English
            $table->string('native_name'); // nome nella lingua stessa
            $table->string('flag')->nullable(); // emoji bandiera
            $table->string('direction')->default('ltr'); // ltr/rtl
            $table->boolean('is_active')->default(false); // Global enable/disable
            $table->boolean('is_default')->default(false); // Platform default
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
};
