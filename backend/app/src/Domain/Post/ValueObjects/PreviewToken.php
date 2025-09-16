<?php

declare(strict_types=1);

namespace App\src\Domain\Post\ValueObjects;

use App\src\Domain\Post\Exceptions\PostValidationException;

final readonly class PreviewToken
{
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public static function generate(): self
    {
        $token = bin2hex(random_bytes(32));
        return new self($token);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getShortForm(): string
    {
        return substr($this->value, 0, 8) . '...';
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new PostValidationException('Preview token cannot be empty');
        }

        if (!preg_match('/^[a-f0-9]{64}$/', $this->value)) {
            throw new PostValidationException('Preview token must be a 64-character hexadecimal string');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(PreviewToken $other): bool
    {
        return $this->value === $other->value;
    }
}