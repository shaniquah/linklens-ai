<?php

return [
    'access_key_id' => env('AWS_ACCESS_KEY_ID'),
    'secret_access_key' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'bucket' => env('AWS_BUCKET'),
    
    'bedrock' => [
        'region' => env('AWS_BEDROCK_REGION', 'us-east-1'),
        'models' => [
            'nova_lite' => 'amazon.nova-lite-v1:0',
            'nova_micro' => 'amazon.nova-micro-v1:0',
            'claude' => 'anthropic.claude-3-sonnet-20240229-v1:0',
        ],
    ],
    
    'bearer_token_bedrock' => env('AWS_BEARER_TOKEN_BEDROCK'),
    'bedrock_region' => env('AWS_BEDROCK_REGION', 'us-east-1'),
    
    'lambda' => [
        'region' => env('AWS_LAMBDA_REGION', 'us-east-1'),
        'functions' => [
            'content_processor' => env('AWS_LAMBDA_CONTENT_PROCESSOR'),
            'connection_analyzer' => env('AWS_LAMBDA_CONNECTION_ANALYZER'),
        ],
    ],
    
    's3' => [
        'region' => env('AWS_S3_REGION', 'us-east-1'),
        'bucket' => env('AWS_S3_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],
];