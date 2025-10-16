<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\ValueObjects;

use InvalidArgumentException;

final readonly class CommentAuthorType
{
    public const REGISTERED = 'registered';
    public const ANONYMOUS = 'anonymous';

    private const VALID_TYPES = [
        self::REGISTERED,
        self::ANONYMOUS,
    ];

    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (!in_array($value, self::VALID_TYPES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid comment author type "%s". Valid types are: %s',
                    $value,
                    implode(', ', self::VALID_TYPES)
                )
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CommentAuthorType $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isRegistered(): bool
    {
        return $this->value === self::REGISTERED;
    }

    public function isAnonymous(): bool
    {
        return $this->value === self::ANONYMOUS;
    }

    public static function registered(): self
    {
        return new self(self::REGISTERED);
    }

    public static function anonymous(): self
    {
        return new self(self::ANONYMOUS);
    }
}
