<?php

return [
    'name' => 'WhiteLabel',
    
    'default_colors' => [
        'primary' => '#3B82F6',
        'secondary' => '#1E40AF',
        'accent' => '#F59E0B',
        'success' => '#10B981',
        'warning' => '#F59E0B',
        'danger' => '#EF4444',
        'background' => '#F3F4F6',
        'sidebar' => '#1F2937',
        'text' => '#111827',
    ],

    'default_font' => 'Inter',

    'whmcs_api_url' => env('WHMCS_API_URL', 'https://billing.example.com/includes/api.php'),
    'whmcs_api_identifier' => env('WHMCS_API_IDENTIFIER', ''),
    'whmcs_api_secret' => env('WHMCS_API_SECRET', ''),

    'default_subdomain_suffix' => '.assocify.app',
    
    'ssl_provider' => 'letsencrypt',
];
