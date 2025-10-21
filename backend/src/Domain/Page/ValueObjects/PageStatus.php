<?php

declare(strict_types=1);

namespace Src\Domain\Page\ValueObjects;

use InvalidArgumentException;

enum PageStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public static function fromString(string $status): self
    {
        return match ($status) {
            'draft' => self::DRAFT,
            'published' => self::PUBLISHED,
            default => throw new InvalidArgumentException("Invalid page status: {$status}")
        };
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public function value(): string
    {
        return $this->value;
    }
}
