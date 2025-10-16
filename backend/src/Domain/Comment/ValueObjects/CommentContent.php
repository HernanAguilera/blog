<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\ValueObjects;

use InvalidArgumentException;

final readonly class CommentContent
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 5000;

    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Comment content must be at least %d characters long. Got %d characters.',
                    self::MIN_LENGTH,
                    $length
                )
            );
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Comment content cannot exceed %d characters. Got %d characters.',
                    self::MAX_LENGTH,
                    $length
                )
            );
        }

        if (trim($value) === '') {
            throw new InvalidArgumentException('Comment content cannot be empty or only whitespace.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CommentContent $other): bool
    {
        return $this->value === $other->value;
    }

    public function length(): int
    {
        return mb_strlen($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
