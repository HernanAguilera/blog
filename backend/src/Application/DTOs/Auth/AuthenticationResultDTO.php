<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Auth;

use Blog\Domain\User\Entities\User;
use DateTimeImmutable;

final readonly class AuthenticationResultDTO
{
    public function __construct(
        public string $token,
        public User $user,
        public DateTimeImmutable $expiresAt
    ) {}
}