<?php

declare(strict_types=1);

namespace Blog\Domain\User\Exceptions;

use Blog\Domain\User\ValueObjects\Email;
use Blog\Domain\User\ValueObjects\UserId;
use DomainException;

final class UserNotFoundException extends DomainException
{
    public function __construct(
        string $message = 'User not found',
        int $code = 404
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(UserId $id): self
    {
        return new self("User with ID '{$id->value()}' not found");
    }

    public static function withEmail(Email $email): self
    {
        return new self("User with email '{$email->value()}' not found");
    }

    public static function general(): self
    {
        return new self('The requested user could not be found');
    }
}