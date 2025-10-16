<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Exceptions;

use DomainException;

final class InvalidAuthorException extends DomainException
{
    public function __construct(
        string $message = 'Invalid comment author data',
        int $code = 400
    ) {
        parent::__construct($message, $code);
    }

    public static function missingUserId(): self
    {
        return new self('Registered comment must have a userId');
    }

    public static function missingAnonymousData(): self
    {
        return new self('Anonymous comment must have anonymousAuthor data');
    }

    public static function invalidAuthorType(): self
    {
        return new self('Comment author type is invalid');
    }

    public static function registeredCannotHaveAnonymousData(): self
    {
        return new self('Registered comment cannot have anonymousAuthor data');
    }

    public static function anonymousCannotHaveUserId(): self
    {
        return new self('Anonymous comment cannot have userId');
    }

    public static function invalidName(string $reason): self
    {
        return new self("Invalid author name: {$reason}");
    }

    public static function invalidEmail(string $email): self
    {
        return new self("Invalid email address: {$email}");
    }

    public static function invalidWebsite(string $website): self
    {
        return new self("Invalid website URL: {$website}");
    }
}
