<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Auth;

final readonly class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}