<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\ValueObjects;

use InvalidArgumentException;

final readonly class AnonymousAuthor
{
    private const MIN_NAME_LENGTH = 2;
    private const MAX_NAME_LENGTH = 100;

    public function __construct(
        private string $name,
        private string $email,
        private ?string $website = null
    ) {
        $this->validateName($name);
        $this->validateEmail($email);

        if ($website !== null) {
            $this->validateWebsite($website);
        }
    }

    private function validateName(string $name): void
    {
        $length = mb_strlen($name);

        if ($length < self::MIN_NAME_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Author name must be at least %d characters long. Got %d characters.',
                    self::MIN_NAME_LENGTH,
                    $length
                )
            );
        }

        if ($length > self::MAX_NAME_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Author name cannot exceed %d characters. Got %d characters.',
                    self::MAX_NAME_LENGTH,
                    $length
                )
            );
        }

        if (trim($name) === '') {
            throw new InvalidArgumentException('Author name cannot be empty or only whitespace.');
        }
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                sprintf('Invalid email address: %s', $email)
            );
        }
    }

    private function validateWebsite(string $website): void
    {
        if (!filter_var($website, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                sprintf('Invalid website URL: %s', $website)
            );
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function website(): ?string
    {
        return $this->website;
    }

    public function equals(AnonymousAuthor $other): bool
    {
        return $this->name === $other->name
            && $this->email === $other->email
            && $this->website === $other->website;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'website' => $this->website,
        ];
    }
}
