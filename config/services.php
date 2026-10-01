<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'arnaru_ai' => [
        'url' => env('ARNARU_AI_URL', 'https://arnaru-ai.vercel.app'),
        'token' => env('ARNARU_AI_TOKEN'),
        'model' => env('ARNARU_AI_MODEL', 'gemini-3-flash'),
        'timeout' => (int) env('ARNARU_AI_TIMEOUT', 120),
        'max_pdf_bytes' => (int) env('ARNARU_AI_MAX_PDF_MB', 4) * 1024 * 1024,
        'models' => [
            'claude-fable-5',
            'claude-fable-5.1',
            'claude-haiku-4.5',
            'claude-opus-4.6',
            'claude-opus-4.7',
            'claude-opus-4.8',
            'claude-sonnet-4',
            'claude-sonnet-4.6',
            'claude-sonnet-5',
            'deepseek-r1',
            'deepseek-v3',
            'deepseek-v3.1',
            'deepseek-v3.2',
            'deepseek-v3.2-online',
            'deepseek-v3.2-think',
            'deepseek-v4-flash',
            'deepseek-v4-pro',
            'deepseek-v3-0324',
            'deepseek-v3-250324',
            'doubao-1.5-pro',
            'doubao-seed-1.8',
            'doubao-v3.5',
            'doubao-v4.1',
            'doubao-v4.2',
            'doubao-v4.5',
            'gemini-2.0-flash',
            'gemini-2.5-flash',
            'gemini-2.5-pro',
            'gemini-3-flash',
            'gemini-3-pro',
            'gemini-3.1-flash',
            'gemini-3.1-pro',
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-3.6-flash',
            'gemini-3.7-flash',
            'gemini-3.8-flash',
            'gpt-3.5',
            'gpt-4.1',
            'gpt-4.1-mini',
            'gpt-4o',
            'gpt-5',
            'gpt-5-mini',
            'gpt-5-nano',
            'gpt-5.1',
            'gpt-5.2',
            'gpt-5.4',
            'gpt-5.5',
            'gpt-5.6-luna',
            'gpt-5.6-sol',
            'gpt-6-astra',
            'gpt-o3-mini',
            'grok-3',
            'grok-3-reasoner',
            'grok-4',
            'grok-4-fast',
            'grok-4-reasoning',
            'grok-4.1',
            'grok-4.1-fast',
            'grok-4.1-reasoning',
            'grok-4.2',
            'grok-4.2-reasoning',
            'grok-4.3-pro',
            'grok-4.3-reasoning',
            'grok-4.5',
            'grok-4.6',
            'kimi-k3',
            'llama-4',
            'llama-4.1',
            'master-v1',
            'minimax-m3',
            'mistral-nemo',
            'mistral-small-3.2',
            'mistral-small-creative',
            'qwen-vl-max',
            'qwen3-235b',
            'qwen3-max',
            'skylark-pro',
            'step-3.5-flash',
            'step-3.5-flash-free',
            'gemma-4-31b',
            'nemotron-3-ultra',
        ],
    ],

];
