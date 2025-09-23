<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Auth;

use Blog\Application\DTOs\Auth\LoginUserDTO;
use Blog\Application\DTOs\Auth\AuthenticationResultDTO;
use Blog\Domain\User\Repositories\UserRepositoryInterface;
use Blog\Application\Services\Auth\JwtServiceInterface;
use Blog\Domain\User\ValueObjects\Email;
use Blog\Domain\User\Exceptions\UserNotFoundException;
use Blog\Domain\User\Exceptions\AuthenticationException;

final readonly class LoginUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JwtServiceInterface $jwtService
    ) {}

    public function execute(LoginUserDTO $dto): AuthenticationResultDTO
    {
        $email = new Email($dto->email);

        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw UserNotFoundException::withEmail($email);
        }

        if (!$user->verifyPassword($dto->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        if (!$user->isActive()) {
            throw AuthenticationException::userNotActive();
        }

        if (!$user->isEmailVerified()) {
            throw AuthenticationException::emailNotVerified();
        }

        $token = $this->jwtService->generateToken($user);

        return new AuthenticationResultDTO(
            token: $token,
            user: $user,
            expiresAt: $this->jwtService->getTokenExpiration($token)
        );
    }
}