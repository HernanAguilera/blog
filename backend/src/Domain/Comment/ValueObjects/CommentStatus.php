<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\ValueObjects;

use InvalidArgumentException;

final readonly class CommentStatus
{
    public const PENDING_APPROVAL = 'pending_approval';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const SPAM = 'spam';

    private const VALID_STATUSES = [
        self::PENDING_APPROVAL,
        self::APPROVED,
        self::REJECTED,
        self::SPAM,
    ];

    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid comment status "%s". Valid statuses are: %s',
                    $value,
                    implode(', ', self::VALID_STATUSES)
                )
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CommentStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return $this->value === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->value === self::REJECTED;
    }

    public function isSpam(): bool
    {
        return $this->value === self::SPAM;
    }

    public static function pendingApproval(): self
    {
        return new self(self::PENDING_APPROVAL);
    }

    public static function approved(): self
    {
        return new self(self::APPROVED);
    }

    public static function rejected(): self
    {
        return new self(self::REJECTED);
    }

    public static function spam(): self
    {
        return new self(self::SPAM);
    }
}
