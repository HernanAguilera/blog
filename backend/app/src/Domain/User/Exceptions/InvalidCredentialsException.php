<?php

declare(strict_types=1);

namespace App\src\Domain\User\Exceptions;

use DomainException;

final class InvalidCredentialsException extends DomainException
{
    public function __construct(
        string $message = 'Invalid credentials provided',
        int $code = 401
    ) {
        parent::__construct($message, $code);
    }

    public static function forLogin(): self
    {
        return new self('The provided email or password is incorrect');
    }

    public static function forToken(): self
    {
        return new self('Invalid or expired authentication token');
    }

    public static function forRefreshToken(): self
    {
        return new self('Invalid or expired refresh token');
    }
}