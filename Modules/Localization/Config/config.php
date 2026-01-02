<?php

return [
    'name' => 'Localization',
    
    'fallback_locale' => 'it',
    
    'cache_ttl' => 3600,
    
    'default_languages' => ['it', 'en', 'de', 'fr', 'es', 'pt', 'nl', 'pl', 'ro', 'el'],
    
    'providers' => [
        'deepl' => [
            'enabled' => true,
            'api_url' => 'https://api-free.deepl.com/v2',
        ],
        'libretranslate' => [
            'enabled' => true,
            'api_url' => 'https://libretranslate.com',
        ],
    ],
    
    'date_formats' => [
        'it' => 'd/m/Y', 
        'en' => 'm/d/Y', 
        'de' => 'd.m.Y',
    ],
    
    'number_formats' => [
        'it' => ['decimal' => ',', 'thousands' => '.'],
        'en' => ['decimal' => '.', 'thousands' => ','],
    ],
    
    'translatable_models' => [
        'Modules\\Events\\Entities\\Event' => ['title', 'description'],
        'Modules\\Documents\\Entities\\Document' => ['title', 'description'],
        'Modules\\Newsletter\\Entities\\Newsletter' => ['subject', 'content'],
    ],
];
