<?php

declare(strict_types=1);

namespace Src\Domain\Page\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final readonly class PageId
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    private function validate(): void
    {
        if (!Uuid::isValid($this->value)) {
            throw new InvalidArgumentException(
                "Invalid PageId format: {$this->value}"
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PageId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
