<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Auth;

use App\src\Application\DTOs\Auth\RegisterUserDTO;
use App\src\Application\DTOs\Auth\AuthenticationResultDTO;
use App\src\Domain\User\Repositories\UserRepositoryInterface;
use App\src\Application\Services\Auth\JwtServiceInterface;
use App\src\Domain\User\Entities\User;
use App\src\Domain\User\ValueObjects\UserId;
use App\src\Domain\User\ValueObjects\Email;
use App\src\Domain\User\ValueObjects\Password;
use App\src\Domain\User\ValueObjects\UserRole;
use App\src\Domain\User\Exceptions\UserAlreadyExistsException;
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