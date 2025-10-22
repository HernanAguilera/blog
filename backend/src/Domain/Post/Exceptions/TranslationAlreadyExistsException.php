<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use DomainException;

final class TranslationAlreadyExistsException extends DomainException
{
    public static function forLocale(string $locale): self
    {
        return new self("Translation already exists for locale '{$locale}'");
    }
}
