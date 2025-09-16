<?php

declare(strict_types=1);

namespace App\src\Domain\Post\ValueObjects;

use App\src\Domain\Post\Exceptions\PostValidationException;

final readonly class HtmlContent
{
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPlainText(): string
    {
        return strip_tags($this->value);
    }

    public function getWordCount(): int
    {
        return str_word_count($this->getPlainText());
    }

    public function getReadingTime(): int
    {
        $wordCount = $this->getWordCount();
        return (int) ceil($wordCount / 200);
    }

    public function isEmpty(): bool
    {
        return empty(trim($this->getPlainText()));
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new PostValidationException('HTML content cannot be empty');
        }

        if (mb_strlen($this->value) > 1000000) {
            throw new PostValidationException('HTML content is too long (max 1MB)');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(HtmlContent $other): bool
    {
        return $this->value === $other->value;
    }
}