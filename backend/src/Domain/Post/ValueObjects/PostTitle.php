<?php

declare(strict_types=1);

namespace Blog\Domain\Post\ValueObjects;

use InvalidArgumentException;

final readonly class PostTitle
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 255;

    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        $trimmedValue = trim($value);

        if (empty($trimmedValue)) {
            throw new InvalidArgumentException('Post title cannot be empty');
        }

        if (mb_strlen($trimmedValue) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Post title must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (mb_strlen($trimmedValue) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Post title cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PostTitle $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}