<?php

declare(strict_types=1);

namespace Src\Domain\Page\Exceptions;

use Exception;

final class PageNotFoundException extends Exception
{
    public static function withId(string $id): self
    {
        return new self("Page with ID {$id} not found");
    }

    public static function withSlug(string $slug): self
    {
        return new self("Page with slug '{$slug}' not found");
    }
}
