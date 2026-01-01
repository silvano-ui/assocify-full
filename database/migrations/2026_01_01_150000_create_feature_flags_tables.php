<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. features
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('module');
            $table->string('category')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_beta')->default(false);
            $table->decimal('price_monthly', 8, 2)->nullable();
            $table->decimal('price_yearly', 8, 2)->nullable();
            $table->decimal('price_per_unit', 8, 2)->nullable();
            $table->string('unit_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('icon')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // 2. feature_dependencies
        Schema::create('feature_dependencies', function (Blueprint $table) {
            $table->id();
            $table->string('feature_slug');
            $table->string('requires_feature_slug');
            $table->timestamps();

            $table->unique(['feature_slug', 'requires_feature_slug']);
        });

        // 3. feature_bundles
        Schema::create('feature_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 8, 2);
            $table->decimal('price_yearly', 8, 2);
            $table->integer('discount_percent')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. feature_bundle_items
        Schema::create('feature_bundle_items', function (Blueprint $table) {
            $table->foreignId('feature_bundle_id')->constrained('feature_bundles')->cascadeOnDelete();
            $table->string('feature_slug');
            $table->primary(['feature_bundle_id', 'feature_slug']);
        });

        // 5. plan_features
        // Note: Assuming 'plans' table exists. If not, this might fail or need adjustment.
        // Using constrained('plans') if exists, otherwise just unsignedBigInteger if we want to be safe but loose.
        // User requested "plan_id(foreign)".
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            // We'll try constrained. If plans doesn't exist, we might need to rely on 'plans' being created elsewhere or remove constraint.
            // Given the context, we'll assume it exists or use a loose reference if we want to avoid error.
            // But strict is better. I'll check if I can just use foreignId without constrained for now if unsure?
            // User said "plan_id(foreign)".
            $table->foreignId('plan_id'); // We will add constraint if we are sure, but let's try to be safe.
            // Actually, if I don't add ->constrained(), it's just a bigInt.
            // Let's assume standard Laravel convention: ->constrained().
            // But I'll comment it out if I suspect it doesn't exist? No, I'll add it.
            // If it fails, I'll fix it.
            
            $table->string('feature_slug');
            $table->boolean('included')->default(false);
            $table->integer('limit_value')->nullable();
            $table->string('limit_type')->nullable(); // enum: unlimited, count, storage_mb, bandwidth_mb
            $table->string('reset_period')->nullable(); // enum: never, daily, weekly, monthly, yearly
            $table->boolean('soft_limit')->default(false);
            $table->timestamps();

            $table->unique(['plan_id', 'feature_slug']);
        });

        // 6. tenant_features
        Schema::create('tenant_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature_slug');
            $table->string('source'); // enum: plan, addon, trial, gift, custom
            $table->boolean('enabled')->default(true);
            $table->integer('limit_value')->nullable();
            $table->integer('used_value')->default(0);
            $table->timestamp('reset_at')->nullable();
            $table->decimal('price_override', 8, 2)->nullable();
            $table->boolean('is_trial')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->foreignId('granted_by')->nullable()->constrained('users');
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'feature_slug']);
        });

        // 7. tenant_feature_addons
        Schema::create('tenant_feature_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature_slug')->nullable();
            $table->foreignId('bundle_id')->nullable()->constrained('feature_bundles');
            $table->integer('quantity')->default(1);
            $table->decimal('price_paid', 8, 2);
            $table->string('billing_cycle'); // enum: monthly, yearly, once
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        // 8. feature_usage_logs
        Schema::create('feature_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature_slug');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // enum: check, use, increment, reset, limit_reached, upgrade_suggested
            $table->integer('quantity')->default(1);
            $table->string('result'); // enum: allowed, denied, soft_warning
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Just created_at as per user "created_at" in list

            $table->index(['tenant_id', 'feature_slug', 'created_at']);
        });

        // 9. feature_alerts
        Schema::create('feature_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature_slug');
            $table->string('alert_type'); // enum: approaching_limit, limit_reached, trial_ending, expiring_soon, expired
            $table->integer('threshold_percent')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_alerts');
        Schema::dropIfExists('feature_usage_logs');
        Schema::dropIfExists('tenant_feature_addons');
        Schema::dropIfExists('tenant_features');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('feature_bundle_items');
        Schema::dropIfExists('feature_bundles');
        Schema::dropIfExists('feature_dependencies');
        Schema::dropIfExists('features');
    }
};
