<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\ValueObjects;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

final readonly class CommentId
{
    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException(
                sprintf('Invalid UUID format for CommentId: %s', $value)
            );
        }
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CommentId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
