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
        Schema::create('domain_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('domain');
            $table->string('tld');
            $table->enum('status', ['available', 'checking', 'pending_registration', 'registered', 'failed', 'transferred']);
            $table->enum('registrar_provider', ['namecheap', 'godaddy', 'cloudflare', 'ovh', 'aruba', 'register_it', 'manual'])->nullable();
            $table->string('registrar_order_id')->nullable();
            $table->integer('registration_years')->default(1);
            $table->decimal('price_paid', 10, 2)->nullable();
            $table->string('currency')->default('EUR');
            $table->json('contact_info')->nullable();
            $table->string('dns_zone_id')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('transfer_code')->nullable();
            $table->enum('transfer_status', ['none', 'pending', 'completed', 'failed'])->default('none');
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('domain_dns_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('tenant_domains')->cascadeOnDelete();
            $table->enum('type', ['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'NS', 'SRV', 'CAA']);
            $table->string('name');
            $table->text('value');
            $table->integer('ttl')->default(3600);
            $table->integer('priority')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('provider_record_id')->nullable();
            $table->timestamps();

            $table->index(['domain_id', 'type']);
        });

        Schema::create('domain_ssl_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('tenant_domains')->cascadeOnDelete();
            $table->enum('provider', ['letsencrypt', 'cloudflare', 'custom'])->default('letsencrypt');
            $table->longText('certificate')->nullable();
            $table->longText('private_key_encrypted')->nullable();
            $table->longText('ca_bundle')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('last_renewal_attempt')->nullable();
            $table->text('renewal_error')->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'revoked', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_ssl_certificates');
        Schema::dropIfExists('domain_dns_records');
        Schema::dropIfExists('domain_registrations');
    }
};
