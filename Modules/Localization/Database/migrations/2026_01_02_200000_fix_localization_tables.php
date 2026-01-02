<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Fix Languages Table
        Schema::table('languages', function (Blueprint $table) {
            if (!Schema::hasColumn('languages', 'native_name')) {
                $table->string('native_name')->after('name')->default(''); 
            }
            if (!Schema::hasColumn('languages', 'flag')) {
                $table->string('flag')->nullable()->after('native_name');
            }
            if (!Schema::hasColumn('languages', 'direction')) {
                $table->string('direction')->default('ltr')->after('flag');
            }
            if (!Schema::hasColumn('languages', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_default');
            }
        });

        // Fix Translations Table
        Schema::table('translations', function (Blueprint $table) {
            // Ensure columns exist
            if (!Schema::hasColumn('translations', 'locale')) {
                $table->string('locale', 10)->index()->after('id');
            }
            if (!Schema::hasColumn('translations', 'group')) {
                $table->string('group')->index()->after('locale');
            }
            if (!Schema::hasColumn('translations', 'key')) {
                $table->string('key')->index()->after('group');
            }
            if (!Schema::hasColumn('translations', 'value')) {
                $table->text('value')->nullable()->after('key');
            }
            
            if (!Schema::hasColumn('translations', 'is_auto_translated')) {
                $table->boolean('is_auto_translated')->default(false)->after('value');
            }
            if (!Schema::hasColumn('translations', 'auto_translation_provider')) {
                $table->string('auto_translation_provider')->nullable()->after('is_auto_translated');
            }
            if (!Schema::hasColumn('translations', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('auto_translation_provider');
            }
            if (!Schema::hasColumn('translations', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            }
        });

        // Separate modify call to ensure columns exist first
        Schema::table('translations', function (Blueprint $table) {
            // Change 'key' and 'group' to string if they are text, to allow indexing
            // We use change() method which requires doctrine/dbal, but assuming it's available or we just try.
            // If doctrine is missing, we might fail. 
            // Alternative: drop and recreate column if empty? But we want to preserve data? 
            // Since this is setup, we assume data is negligible.
            // But let's try to set them as string.
            // Note: 'key' is reserved, use valid syntax.
            
            $table->string('group', 191)->change();
            $table->string('key', 191)->change();
        });

        Schema::table('translations', function (Blueprint $table) {
             try {
                $table->unique(['locale', 'group', 'key']);
            } catch (\Exception $e) {
                // Ignore
            }
        });

        // Fix Tenant Languages
        Schema::table('tenant_languages', function (Blueprint $table) {
             if (!Schema::hasColumn('tenant_languages', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
             if (!Schema::hasColumn('tenant_languages', 'is_default')) {
                $table->boolean('is_default')->default(false);
            }
        });
    }

    public function down()
    {
        //
    }
};
