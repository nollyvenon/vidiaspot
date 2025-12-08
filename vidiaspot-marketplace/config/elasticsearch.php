<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration options determine how the application connects to
    | Elasticsearch for advanced search functionality.
    |
    */

    'host' => env('ELASTICSEARCH_HOST', 'http://localhost:9200'),
    
    'index' => env('ELASTICSEARCH_INDEX', 'vidiaspot_marketplace'),
    
    'username' => env('ELASTICSEARCH_USERNAME'),
    
    'password' => env('ELASTICSEARCH_PASSWORD'),
    
    // SSL Configuration
    'ssl_verification' => env('ELASTICSEARCH_SSL_VERIFY', true),
    
    // Timeout settings
    'connection_timeout' => env('ELASTICSEARCH_CONNECTION_TIMEOUT', 30),
    'request_timeout' => env('ELASTICSEARCH_REQUEST_TIMEOUT', 30),
    
    // Additional hosts for cluster setup
    'hosts' => explode(',', env('ELASTICSEARCH_HOSTS', '')),
    
    // Default search settings
    'default_size' => env('ELASTICSEARCH_DEFAULT_SIZE', 20),
    'max_size' => env('ELASTICSEARCH_MAX_SIZE', 1000),
    
    // Index mapping settings
    'mapping' => [
        'ad' => [
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                    'fields' => [
                        'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                    ]
                ],
                'description' => ['type' => 'text', 'analyzer' => 'standard'],
                'price' => ['type' => 'double'],
                'location' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                    'fields' => [
                        'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                    ]
                ],
                'category_id' => ['type' => 'integer'],
                'category_name' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                    'fields' => [
                        'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                    ]
                ],
                'user_id' => ['type' => 'integer'],
                'status' => ['type' => 'keyword'],
                'created_at' => ['type' => 'date'],
                'updated_at' => ['type' => 'date'],
                'tags' => ['type' => 'keyword']
            ]
        ]
    ]
];