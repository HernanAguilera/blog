<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use RuntimeException;

/**
 * Exception thrown when locale configuration is missing or invalid.
 * This helps catch configuration errors early rather than silently using defaults.
 */
final class LocaleConfigurationException extends RuntimeException
{
    public static function missingSupportedLocales(): self
    {
        return new self(
            "Locale configuration error: 'locales.supported' is not defined. " .
            "Please define supported locales in config/locales.php"
        );
    }

    public static function invalidSupportedLocales(): self
    {
        return new self(
            "Locale configuration error: 'locales.supported' must be a non-empty array. " .
            "Check config/locales.php"
        );
    }

    public static function missingDefaultLocale(): self
    {
        return new self(
            "Locale configuration error: 'locales.default' is not defined. " .
            "Please define default locale in config/locales.php"
        );
    }

    public static function invalidDefaultLocale(): self
    {
        return new self(
            "Locale configuration error: 'locales.default' must be a non-empty string. " .
            "Check config/locales.php"
        );
    }

    public static function missingLocaleNames(): self
    {
        return new self(
            "Locale configuration error: 'locales.names' is not defined. " .
            "Please define locale names in config/locales.php"
        );
    }

    public static function invalidLocaleNames(): self
    {
        return new self(
            "Locale configuration error: 'locales.names' must be an array. " .
            "Check config/locales.php"
        );
    }
}
