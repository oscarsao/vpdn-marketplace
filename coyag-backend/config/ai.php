<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the AI provider and model settings for the platform.
    | Supported providers: 'openai', 'gemini', 'anthropic'
    |
    */

    'provider' => env('AI_PROVIDER', 'openai'),

    'providers' => [

        'openai' => [
            'api_key'    => env('AI_OPENAI_API_KEY'),
            'model'      => env('AI_OPENAI_MODEL', 'gpt-4o'),
            'max_tokens' => env('AI_OPENAI_MAX_TOKENS', 4096),
            'base_url'   => env('AI_OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],

        'gemini' => [
            'api_key'    => env('AI_GEMINI_API_KEY'),
            'model'      => env('AI_GEMINI_MODEL', 'gemini-2.0-flash'),
            'max_tokens' => env('AI_GEMINI_MAX_TOKENS', 4096),
            'base_url'   => env('AI_GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        ],

        'anthropic' => [
            'api_key'    => env('AI_ANTHROPIC_API_KEY'),
            'model'      => env('AI_ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
            'max_tokens' => env('AI_ANTHROPIC_MAX_TOKENS', 4096),
            'base_url'   => env('AI_ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific AI features.
    |
    */

    'features' => [
        'business_analysis'  => env('AI_FEATURE_BUSINESS_ANALYSIS', true),
        'recommendations'    => env('AI_FEATURE_RECOMMENDATIONS', true),
        'content_generation' => env('AI_FEATURE_CONTENT_GENERATION', true),
        'document_analysis'  => env('AI_FEATURE_DOCUMENT_ANALYSIS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'requests_per_minute' => env('AI_RATE_LIMIT', 30),
    ],

];
