<?php

declare(strict_types=1);

namespace Src\Domain\Page\ValueObjects;

use InvalidArgumentException;

final readonly class PageTitle
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $title): self
    {
        return new self(trim($title));
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('Page title cannot be empty');
        }

        if (mb_strlen($this->value) > 255) {
            throw new InvalidArgumentException('Page title cannot exceed 255 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PageTitle $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
