<?php

declare(strict_types=1);

namespace Blog\Domain\Post\ValueObjects;

use InvalidArgumentException;

final readonly class PostSlug
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 255;
    private const SLUG_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        $trimmedValue = trim($value);

        if (empty($trimmedValue)) {
            throw new InvalidArgumentException('Post slug cannot be empty');
        }

        if (mb_strlen($trimmedValue) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Post slug must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (mb_strlen($trimmedValue) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Post slug cannot exceed %d characters', self::MAX_LENGTH)
            );
        }

        if (!preg_match(self::SLUG_PATTERN, $trimmedValue)) {
            throw new InvalidArgumentException(
                'Post slug must contain only lowercase letters, numbers and hyphens'
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PostSlug $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromTitle(string $title): self
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return new self($slug);
    }
}