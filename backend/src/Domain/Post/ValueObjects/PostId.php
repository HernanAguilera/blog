<?php

declare(strict_types=1);

namespace Blog\Domain\Post\ValueObjects;

use InvalidArgumentException;

final readonly class PostId
{
    public function __construct(
        private int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Post ID must be a positive integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(PostId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}