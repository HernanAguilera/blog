<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use DomainException;

final class InvalidPostLocaleException extends DomainException
{
    public static function unsupportedLocale(string $locale, array $supported): self
    {
        $supportedList = implode(', ', $supported);
        return new self(
            "Locale '{$locale}' is not supported. Supported locales: {$supportedList}"
        );
    }
}
