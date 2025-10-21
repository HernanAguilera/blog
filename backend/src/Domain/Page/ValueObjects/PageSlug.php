<?php

declare(strict_types=1);

namespace Src\Domain\Page\ValueObjects;

use Src\Domain\Page\Exceptions\InvalidPageSlugException;

final readonly class PageSlug
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $slug): self
    {
        return new self(self::sanitize($slug));
    }

    private static function sanitize(string $slug): string
    {
        // Convert to lowercase
        $slug = mb_strtolower($slug, 'UTF-8');

        // Replace spaces with hyphens
        $slug = str_replace(' ', '-', $slug);

        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim hyphens from start and end
        return trim($slug, '-');
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidPageSlugException('Page slug cannot be empty');
        }

        if (strlen($this->value) > 100) {
            throw new InvalidPageSlugException('Page slug cannot exceed 100 characters');
        }

        // Only lowercase letters, numbers, and hyphens
        if (!preg_match('/^[a-z0-9\-]+$/', $this->value)) {
            throw new InvalidPageSlugException(
                'Page slug can only contain lowercase letters, numbers, and hyphens'
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PageSlug $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
