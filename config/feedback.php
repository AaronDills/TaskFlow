<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feedback & Feature Request Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the restricted feedback and feature
    | request system, including allowed email addresses, AI provider settings,
    | and GitHub integration.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Allowed Email Addresses
    |--------------------------------------------------------------------------
    |
    | Only users with these email addresses will be able to access the
    | feedback and feature request page. Add emails as comma-separated
    | values in your .env file.
    |
    */

    'allowed_emails' => array_filter(
        array_map('trim', explode(',', env('FEEDBACK_ALLOWED_EMAILS', '')))
    ),

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Choose which AI provider to use for analyzing feature requests.
    | Supported: "claude", "openai"
    |
    */

    'ai_provider' => env('FEEDBACK_AI_PROVIDER', 'claude'),

    // Claude (Anthropic) API settings
    'claude_api_key' => env('CLAUDE_API_KEY'),
    'claude_model' => env('CLAUDE_MODEL', 'claude-sonnet-4-20250514'),

    // OpenAI API settings
    'openai_api_key' => env('OPENAI_API_KEY'),
    'openai_model' => env('OPENAI_MODEL', 'gpt-4'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Integration
    |--------------------------------------------------------------------------
    |
    | Configure GitHub integration for automatically creating issues or
    | pull requests from processed feature requests.
    |
    */

    'github_token' => env('GITHUB_TOKEN'),
    'github_repo_owner' => env('GITHUB_REPO_OWNER'),
    'github_repo_name' => env('GITHUB_REPO_NAME'),

];
