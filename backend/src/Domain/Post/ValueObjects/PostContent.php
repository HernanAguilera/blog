<?php

declare(strict_types=1);

namespace Blog\Domain\Post\ValueObjects;

use InvalidArgumentException;

final readonly class PostContent
{
    private const MIN_LENGTH = 10;
    private const MAX_LENGTH = 100000; // 100KB max content

    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        $trimmedValue = trim($value);

        if (empty($trimmedValue)) {
            throw new InvalidArgumentException('Post content cannot be empty');
        }

        if (mb_strlen($trimmedValue) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Post content must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (mb_strlen($trimmedValue) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Post content cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PostContent $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getPlainText(): string
    {
        return strip_tags($this->value);
    }

    public function getWordCount(): int
    {
        $plainText = $this->getPlainText();
        $words = str_word_count($plainText);

        return $words;
    }
}