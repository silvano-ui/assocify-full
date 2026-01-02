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
        Schema::create('tenant_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('locale', 10);
            $table->string('group');
            $table->string('key');
            $table->text('value')->nullable();
            $table->boolean('is_auto_translated')->default(false);
            $table->timestamps();

            // $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->unique(['tenant_id', 'locale', 'group', 'key'], 'tenant_translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_translations');
    }
};
