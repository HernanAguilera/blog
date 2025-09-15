<?php

declare(strict_types=1);

namespace App\src\Application\DTOs\Auth;

use App\src\Domain\User\Entities\User;
use DateTimeImmutable;

final readonly class AuthenticationResultDTO
{
    public function __construct(
        public string $token,
        public User $user,
        public DateTimeImmutable $expiresAt
    ) {}
}