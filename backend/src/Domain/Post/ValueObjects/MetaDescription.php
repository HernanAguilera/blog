<?php

declare(strict_types=1);

namespace Blog\Domain\Post\ValueObjects;

use InvalidArgumentException;

final readonly class MetaDescription
{
    private const MAX_LENGTH = 160; // SEO best practice
    private const MIN_LENGTH = 50;  // Minimum for effective SEO

    public function __construct(
        private ?string $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    private function validate(string $value): void
    {
        $trimmedValue = trim($value);

        if (empty($trimmedValue)) {
            throw new InvalidArgumentException('Meta description cannot be empty when provided');
        }

        if (mb_strlen($trimmedValue) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Meta description should be at least %d characters for better SEO', self::MIN_LENGTH)
            );
        }

        if (mb_strlen($trimmedValue) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Meta description cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function equals(MetaDescription $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }

    public function isEmpty(): bool
    {
        return $this->value === null || trim($this->value) === '';
    }

    public function isOptimal(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        $length = mb_strlen(trim($this->value));
        return $length >= self::MIN_LENGTH && $length <= self::MAX_LENGTH;
    }

    public static function fromContent(PostContent $content): self
    {
        $plainText = $content->getPlainText();
        $excerpt = mb_substr($plainText, 0, self::MAX_LENGTH);

        // Try to cut at word boundary
        $lastSpace = mb_strrpos($excerpt, ' ');
        if ($lastSpace !== false && $lastSpace > self::MIN_LENGTH) {
            $excerpt = mb_substr($excerpt, 0, $lastSpace);
        }

        return new self($excerpt . '...');
    }
}