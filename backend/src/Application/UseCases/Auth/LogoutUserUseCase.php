<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Auth;

use Blog\Application\Services\Auth\JwtServiceInterface;

final readonly class LogoutUserUseCase
{
    public function __construct(
        private JwtServiceInterface $jwtService
    ) {}

    public function execute(string $token): bool
    {
        return $this->jwtService->invalidateToken($token);
    }
}