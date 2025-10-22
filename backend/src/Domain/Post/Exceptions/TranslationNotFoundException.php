<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use DomainException;

final class TranslationNotFoundException extends DomainException
{
    public static function forPostAndLocale(string $postId, string $locale): self
    {
        return new self(
            "Translation not found for post '{$postId}' in locale '{$locale}'"
        );
    }

    public static function forSlug(string $slug): self
    {
        return new self("Translation not found for slug '{$slug}'");
    }
}
