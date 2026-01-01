<?php

namespace Modules\WhiteLabel\Console;

use Illuminate\Console\Command;
use Modules\WhiteLabel\Services\WhmcsApiService;

class CheckDomainAvailability extends Command
{
    protected $signature = 'whmcs:check-domain {domain}';
    protected $description = 'Check domain availability via WHMCS';

    public function handle()
    {
        $whmcs = new WhmcsApiService();
        $domain = $this->argument('domain');
        
        $this->info("Checking availability for: {$domain}");
        
        if (!$whmcs->isConfigured()) {
            $this->error('WHMCS API not configured.');
            return 1;
        }
        
        $result = $whmcs->checkDomainAvailability($domain);
        
        if ($result['success']) {
            $status = $result['data']['status'] ?? 'unknown';
            if ($status === 'available') {
                $this->info("✅ {$domain} is AVAILABLE!");
            } else {
                $this->warn("❌ {$domain} is NOT available ({$status})");
            }
            return 0;
        }
        
        $this->error('Error: ' . ($result['error'] ?? 'Unknown'));
        return 1;
    }
}
