<?php

declare(strict_types=1);

namespace App\src\Application\DTOs\Auth;

final readonly class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}