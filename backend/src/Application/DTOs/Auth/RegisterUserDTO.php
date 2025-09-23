<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Auth;

use Blog\Domain\User\ValueObjects\UserRole;

final readonly class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?UserRole $role = null
    ) {}
}