<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Editor Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the advanced post editor functionality including
    | auto-save, preview system, HTML sanitization, and cleanup settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Content Limits
    |--------------------------------------------------------------------------
    */

    // Maximum content size in bytes (1MB = 1048576 bytes)
    'max_content_size' => env('EDITOR_MAX_CONTENT_SIZE', 1048576),

    // Auto-save interval on frontend (seconds)
    'autosave_interval' => env('EDITOR_AUTOSAVE_INTERVAL', 30),

    /*
    |--------------------------------------------------------------------------
    | TTL Settings (Time To Live)
    |--------------------------------------------------------------------------
    */

    // Draft TTL in hours (default 24 hours)
    'draft_ttl_hours' => env('EDITOR_DRAFT_TTL_HOURS', 24),

    // Preview default TTL in hours (default 24 hours)
    'preview_ttl_hours' => env('EDITOR_PREVIEW_TTL_HOURS', 24),

    // Maximum preview TTL allowed (default 1 week = 168 hours)
    'max_preview_ttl_hours' => env('EDITOR_MAX_PREVIEW_TTL_HOURS', 168),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    // Auto-save rate limit (requests per minute)
    'autosave_rate_limit' => env('EDITOR_AUTOSAVE_RATE_LIMIT', 30),

    // Preview generation rate limit (requests per minute)
    'preview_rate_limit' => env('EDITOR_PREVIEW_RATE_LIMIT', 5),

    // Preview access rate limit for public URLs (requests per minute)
    'preview_access_rate_limit' => env('EDITOR_PREVIEW_ACCESS_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | HTML Sanitizer Configuration
    |--------------------------------------------------------------------------
    */

    'html_sanitizer' => [

        // Default allowed HTML tags for posts
        'allowed_tags' => [
            'p',
            'br',
            'strong',
            'em',
            'u',
            'i',
            'b',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ul',
            'ol',
            'li',
            'blockquote',
            'cite',
            'a[href|title|target|rel]',
            'img[src|alt|width|height|class]',
            'pre[class]',
            'code[class]',
            'table[class]',
            'thead',
            'tbody',
            'tr',
            'td[colspan|rowspan|class]',
            'th[colspan|rowspan|class]',
            'span[class]',
            'div[class]',
            'small'
        ],

        // Allowed CSS classes (for syntax highlighting and styling)
        'allowed_classes' => [
            // Syntax highlighting classes (highlight.js)
            'hljs',
            'hljs-*',
            'language-*',
            'lang-*',

            // Common content classes
            'code-block',
            'highlight',
            'inline-code',

            // Table styling
            'table',
            'table-responsive',
            'table-striped',
            'table-bordered',

            // Alignment classes
            'text-center',
            'text-left',
            'text-right',
            'text-justify',

            // Image classes
            'image-responsive',
            'img-fluid',
            'figure-img',

            // Content styling
            'blockquote-footer',
            'lead',
            'small',

            // Custom editor classes
            'editor-*',
            'content-*'
        ],

        // Additional configuration passed to HTMLPurifier
        'purifier_config' => [
            'HTML.MaxImgLength' => 1200,
            'HTML.TargetBlank' => true,
            'Attr.AllowedFrameTargets' => ['_blank', '_self'],
            'URI.DisableExternalResources' => false,
            'URI.AllowedSchemes' => ['http' => true, 'https' => true, 'data' => true],
            'Output.FlashCompat' => false,
            'HTML.FlashAllowFullScreen' => false,
        ],

        // More restrictive settings for preview mode
        'preview_restrictions' => [
            'disable_external_links' => env('EDITOR_PREVIEW_DISABLE_EXTERNAL', true),
            'disable_images' => env('EDITOR_PREVIEW_DISABLE_IMAGES', false),
            'max_content_length' => env('EDITOR_PREVIEW_MAX_LENGTH', 50000), // 50KB for previews
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    */

    'cleanup' => [
        // How often to run cleanup (affects manual cleanup commands)
        'batch_size' => env('EDITOR_CLEANUP_BATCH_SIZE', 1000),

        // Cleanup older than X hours (for manual cleanup with --older-than)
        'default_older_than_hours' => env('EDITOR_CLEANUP_OLDER_THAN', 72), // 3 days

        // Log cleanup operations
        'log_operations' => env('EDITOR_LOG_CLEANUP', true),

        // Alert when deletion count exceeds threshold
        'alert_threshold' => env('EDITOR_CLEANUP_ALERT_THRESHOLD', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        // Validate CSRF token for auto-save (additional security layer)
        'require_csrf_for_autosave' => env('EDITOR_REQUIRE_CSRF_AUTOSAVE', false),

        // Log suspicious activity (rapid preview generation, etc.)
        'log_suspicious_activity' => env('EDITOR_LOG_SUSPICIOUS', true),

        // Maximum number of drafts per user
        'max_drafts_per_user' => env('EDITOR_MAX_DRAFTS_PER_USER', 50),

        // Maximum number of previews per user per day
        'max_previews_per_user_per_day' => env('EDITOR_MAX_PREVIEWS_PER_DAY', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */

    'features' => [
        // Enable/disable auto-save functionality
        'enable_autosave' => env('EDITOR_ENABLE_AUTOSAVE', true),

        // Enable/disable preview functionality
        'enable_preview' => env('EDITOR_ENABLE_PREVIEW', true),

        // Enable/disable draft restoration
        'enable_draft_restore' => env('EDITOR_ENABLE_DRAFT_RESTORE', true),

        // Enable/disable automatic cleanup jobs
        'enable_auto_cleanup' => env('EDITOR_ENABLE_AUTO_CLEANUP', true),

        // Enable/disable detailed logging
        'enable_detailed_logging' => env('EDITOR_ENABLE_DETAILED_LOGGING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */

    'performance' => [
        // Cache HTMLPurifier configuration
        'cache_purifier_config' => env('EDITOR_CACHE_PURIFIER', true),

        // Use database transactions for consistency
        'use_database_transactions' => env('EDITOR_USE_DB_TRANSACTIONS', true),

        // Queue cleanup jobs instead of running them synchronously
        'queue_cleanup_jobs' => env('EDITOR_QUEUE_CLEANUP', true),

        // Optimize database queries with indexes
        'optimize_queries' => env('EDITOR_OPTIMIZE_QUERIES', true),
    ]

];