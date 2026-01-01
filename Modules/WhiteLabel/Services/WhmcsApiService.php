<?php

namespace Modules\WhiteLabel\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhmcsApiService
{
    protected string $apiUrl;
    protected string $apiIdentifier;
    protected string $apiSecret;
    
    public function __construct()
    {
        $this->apiUrl = config('whitelabel.whmcs.api_url', '');
        $this->apiIdentifier = config('whitelabel.whmcs.api_identifier', '');
        $this->apiSecret = config('whitelabel.whmcs.api_secret', '');
    }
    
    protected function makeApiCall(string $action, array $params = []): array
    {
        try {
            $postData = array_merge([
                'action' => $action,
                'identifier' => $this->apiIdentifier,
                'secret' => $this->apiSecret,
                'responsetype' => 'json',
            ], $params);
            
            $response = Http::asForm()->timeout(30)->post($this->apiUrl, $postData);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']) && $data['result'] === 'success') {
                    return ['success' => true, 'data' => $data];
                }
                return ['success' => false, 'error' => $data['message'] ?? 'Unknown error', 'data' => $data];
            }
            return ['success' => false, 'error' => 'HTTP Error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('WHMCS API Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->apiIdentifier) && !empty($this->apiSecret);
    }
    
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WHMCS API not configured'];
        }
        return $this->makeApiCall('GetAdminDetails');
    }
    
    public function checkDomainAvailability(string $domain): array
    {
        return $this->makeApiCall('DomainWhois', ['domain' => $domain]);
    }
    
    public function registerDomain(string $domain, int $clientId, array $nameservers = [], int $years = 1): array
    {
        $params = [
            'domain' => $domain,
            'domaintype' => 'Register',
            'regperiod' => $years,
            'clientid' => $clientId,
            'paymentmethod' => 'mailin',
        ];
        foreach ($nameservers as $i => $ns) {
            $params['ns' . ($i + 1)] = $ns;
        }
        return $this->makeApiCall('AddOrder', $params);
    }
    
    public function transferDomain(string $domain, int $clientId, string $eppCode): array
    {
        return $this->makeApiCall('AddOrder', [
            'domain' => $domain,
            'domaintype' => 'Transfer',
            'eppcode' => $eppCode,
            'clientid' => $clientId,
            'paymentmethod' => 'mailin',
        ]);
    }
    
    public function renewDomain(string $domain, int $years = 1): array
    {
        return $this->makeApiCall('DomainRenew', ['domain' => $domain, 'regperiod' => $years]);
    }
    
    public function getDomainInfo(string $domain): array
    {
        return $this->makeApiCall('GetDomainInfo', ['domain' => $domain]);
    }
}
