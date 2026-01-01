<?php

return [
    'name' => 'WhiteLabel',
    'whmcs' => [
        'api_url' => env('WHMCS_API_URL', ''),
        'api_identifier' => env('WHMCS_API_IDENTIFIER', ''),
        'api_secret' => env('WHMCS_API_SECRET', ''),
    ],
    'default_subdomain_suffix' => env('WHITELABEL_SUBDOMAIN_SUFFIX', '.assocify.app'),
    'default_nameservers' => ['ns1.bluix.net', 'ns2.bluix.net'],
];
