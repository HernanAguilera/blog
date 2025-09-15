<?php

declare(strict_types=1);

namespace App\src\Domain\User\ValueObjects;

use InvalidArgumentException;

final readonly class Password
{
    private const MIN_LENGTH = 8;
    private const MAX_LENGTH = 255;

    public function __construct(
        private string $value
    ) {
        $this->validatePassword($value);
    }

    public static function fromPlainText(string $plainPassword): self
    {
        $instance = new self($plainPassword);
        return new self(password_hash($plainPassword, PASSWORD_ARGON2ID));
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->value);
    }

    public function needsRehash(): bool
    {
        return password_needs_rehash($this->value, PASSWORD_ARGON2ID);
    }

    private function validatePassword(string $password): void
    {
        if (strlen($password) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Password must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (strlen($password) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Password cannot exceed %d characters', self::MAX_LENGTH)
            );
        }

        // Additional security validation for plain text passwords
        if (!password_get_info($password)['algo']) {
            $this->validatePlainTextPassword($password);
        }
    }

    private function validatePlainTextPassword(string $password): void
    {
        // Simplified validation for development
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }

        // For production, enable stricter validation:
        /*
        if (!preg_match('/[A-Z]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one uppercase letter');
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one lowercase letter');
        }
        */

        /*
        if (!preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one number');
        }
        */

        /*
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one special character');
        }
        */
    }
}