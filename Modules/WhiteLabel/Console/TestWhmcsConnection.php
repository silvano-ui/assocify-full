<?php

namespace Modules\WhiteLabel\Console;

use Illuminate\Console\Command;
use Modules\WhiteLabel\Services\WhmcsApiService;

class TestWhmcsConnection extends Command
{
    protected $signature = 'whmcs:test';
    protected $description = 'Test WHMCS API connection';

    public function handle()
    {
        $whmcs = new WhmcsApiService();
        
        $this->info('Testing WHMCS API connection...');
        
        if (!$whmcs->isConfigured()) {
            $this->error('WHMCS API not configured. Set WHMCS_API_URL, WHMCS_API_IDENTIFIER, WHMCS_API_SECRET in .env');
            return 1;
        }
        
        $this->info('API URL: ' . config('whitelabel.whmcs.api_url'));
        
        $result = $whmcs->testConnection();
        
        if ($result['success']) {
            $this->info('✅ Connection successful!');
            return 0;
        }
        
        $this->error('❌ Connection failed: ' . ($result['error'] ?? 'Unknown error'));
        return 1;
    }
}
