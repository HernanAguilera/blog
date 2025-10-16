<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Comment Moderation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the comment moderation system. These settings control
    | how comments are handled, auto-approved, and monitored for spam.
    |
    */

    'comments' => [
        /*
         * Auto-approve comments from registered users
         */
        'auto_approve_registered' => env('MODERATION_AUTO_APPROVE_REGISTERED', true),

        /*
         * Auto-approve comments from anonymous users
         * WARNING: Setting to true bypasses moderation for anonymous comments
         */
        'auto_approve_anonymous' => env('MODERATION_AUTO_APPROVE_ANONYMOUS', false),

        /*
         * Enable spam detection using CommentDomainService rules
         */
        'spam_detection_enabled' => env('MODERATION_SPAM_DETECTION', true),

        /*
         * Enable profanity/content quality filtering
         */
        'profanity_filter_enabled' => env('MODERATION_PROFANITY_FILTER', true),

        /*
         * Maximum number of links allowed in a comment before flagging as spam
         */
        'max_links_allowed' => env('MODERATION_MAX_LINKS', 3),

        /*
         * Enable email notifications for pending comments
         */
        'notifications_enabled' => env('MODERATION_NOTIFICATIONS_ENABLED', true),

        /*
         * Comma-separated list of email addresses to notify
         * If empty, will notify all admin users
         */
        'notify_emails' => env('MODERATION_NOTIFY_EMAILS', ''),
    ],

];
