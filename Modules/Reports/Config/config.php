<?php

return [
    'name' => 'Reports',

    'export_formats' => ['pdf', 'xlsx', 'csv'],
    
    'retention_days' => 90,
    
    'max_export_rows' => 100000,
    
    'streaming_threshold' => 10000,
    
    'cache_ttl' => 900,
    
    'share_link_expiry_days' => 30,

    'data_sources' => [
        'members' => \Modules\Reports\Services\DataSources\MembersDataSource::class,
        'payments' => \Modules\Reports\Services\DataSources\PaymentsDataSource::class,
        // Add other data sources here
    ],
    
    'engagement' => [
        'weights' => [
            'events' => 30, 
            'payments' => 25, 
            'activity' => 25, 
            'age' => 10, 
            'profile' => 10
        ],
        'segments' => [
            'highly_active' => 80, 
            'active' => 60, 
            'moderate' => 40, 
            'at_risk' => 20, 
            'dormant' => 1
        ]
    ],
    
    'fiscal' => [
        'enabled' => true, 
        'country' => 'IT'
    ],
    
    'webhooks' => [
        'timeout' => 30, 
        'max_attempts' => 3
    ],
];
