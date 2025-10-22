<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | List of supported locales for the application.
    | The first locale in the array is considered the default.
    |
    */
    'supported' => ['es', 'en', 'pt'],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale for the application.
    | This locale will be used as fallback when a translation is not available.
    |
    */
    'default' => 'es',

    /*
    |--------------------------------------------------------------------------
    | Locale Names
    |--------------------------------------------------------------------------
    |
    | Human-readable names for each locale.
    |
    */
    'names' => [
        'es' => 'Español',
        'en' => 'English',
        'pt' => 'Português',
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Flags
    |--------------------------------------------------------------------------
    |
    | Flag emojis for each locale (optional, for UI display).
    |
    */
    'flags' => [
        'es' => '🇪🇸',
        'en' => '🇬🇧',
        'pt' => '🇵🇹',
    ],
];
