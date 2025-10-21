<?php

declare(strict_types=1);

namespace Src\Domain\Page\ValueObjects;

final readonly class PageContent
{
    private function __construct(
        private string $value
    ) {
    }

    public static function fromString(string $content): self
    {
        return new self($content);
    }

    public static function empty(): self
    {
        return new self('');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return empty(trim($this->value));
    }

    public function equals(PageContent $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
