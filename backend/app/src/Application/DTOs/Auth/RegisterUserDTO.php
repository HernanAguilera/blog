<?php

declare(strict_types=1);

namespace App\src\Application\DTOs\Auth;

use App\src\Domain\User\ValueObjects\UserRole;

final readonly class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?UserRole $role = null
    ) {}
}