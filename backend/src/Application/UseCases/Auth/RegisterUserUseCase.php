<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Auth;

use Blog\Application\DTOs\Auth\RegisterUserDTO;
use Blog\Application\DTOs\Auth\AuthenticationResultDTO;
use Blog\Domain\User\Repositories\UserRepositoryInterface;
use Blog\Application\Services\Auth\JwtServiceInterface;
use Blog\Domain\User\Entities\User;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\User\ValueObjects\Email;
use Blog\Domain\User\ValueObjects\Password;
use Blog\Domain\User\ValueObjects\UserRole;
use Blog\Domain\User\Exceptions\UserAlreadyExistsException;
use DateTimeImmutable;

final readonly class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JwtServiceInterface $jwtService
    ) {}

    public function execute(RegisterUserDTO $dto): AuthenticationResultDTO
    {
        $email = new Email($dto->email);

        if ($this->userRepository->findByEmail($email)) {
            throw UserAlreadyExistsException::withEmail($email);
        }

        $user = new User(
            id: new UserId(1), // Temporal - será reemplazado por auto-increment
            name: $dto->name,
            email: $email,
            password: Password::fromPlainText($dto->password),
            role: $dto->role ?? UserRole::GUEST,
            isActive: true,
            createdAt: new DateTimeImmutable()
        );

        $savedUser = $this->userRepository->save($user);
        $token = $this->jwtService->generateToken($savedUser);

        return new AuthenticationResultDTO(
            token: $token,
            user: $savedUser,
            expiresAt: $this->jwtService->getTokenExpiration($token)
        );
    }
}