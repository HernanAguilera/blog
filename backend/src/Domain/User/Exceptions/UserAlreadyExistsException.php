<?php

declare(strict_types=1);

namespace Blog\Domain\User\Exceptions;

use Blog\Domain\User\ValueObjects\Email;
use DomainException;

final class UserAlreadyExistsException extends DomainException
{
    public function __construct(
        string $message = 'User already exists',
        int $code = 409
    ) {
        parent::__construct($message, $code);
    }

    public static function withEmail(Email $email): self
    {
        return new self("User with email '{$email->value()}' already exists");
    }
}