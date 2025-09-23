<?php

declare(strict_types=1);

namespace Blog\Domain\User\Exceptions;

use DomainException;

final class AuthenticationException extends DomainException
{
    public function __construct(
        string $message = 'Authentication failed',
        int $code = 401
    ) {
        parent::__construct($message, $code);
    }

    public static function userNotActive(): self
    {
        return new self('User account is not active', 403);
    }

    public static function emailNotVerified(): self
    {
        return new self('Email address has not been verified', 403);
    }

    public static function tokenExpired(): self
    {
        return new self('Authentication token has expired');
    }

    public static function tokenBlacklisted(): self
    {
        return new self('Authentication token has been revoked');
    }

    public static function insufficientPermissions(): self
    {
        return new self('Insufficient permissions to perform this action', 403);
    }

    public static function sessionExpired(): self
    {
        return new self('Your session has expired. Please log in again');
    }

    public static function tooManyAttempts(): self
    {
        return new self('Too many authentication attempts. Please try again later', 429);
    }

    public static function accountLocked(): self
    {
        return new self('Account has been temporarily locked due to multiple failed attempts', 423);
    }
}