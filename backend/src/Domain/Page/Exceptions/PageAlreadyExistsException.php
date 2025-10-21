<?php

declare(strict_types=1);

namespace Src\Domain\Page\Exceptions;

use Exception;

final class PageAlreadyExistsException extends Exception
{
    public static function withSlug(string $slug): self
    {
        return new self("A page with slug '{$slug}' already exists");
    }
}
