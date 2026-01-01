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
        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->enum('verification_method', ['dns_txt', 'dns_cname', 'file'])->nullable()->after('is_verified');
            $table->string('registrar')->nullable()->after('verification_method');
            $table->date('registration_date')->nullable()->after('registrar');
            $table->date('expiry_date')->nullable()->after('registration_date');
            $table->boolean('auto_renew')->default(true)->after('expiry_date');
            $table->json('nameservers')->nullable()->after('auto_renew');
            $table->boolean('whois_privacy')->default(true)->after('nameservers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->dropColumn([
                'verification_method',
                'registrar',
                'registration_date',
                'expiry_date',
                'auto_renew',
                'nameservers',
                'whois_privacy',
            ]);
        });
    }
};
