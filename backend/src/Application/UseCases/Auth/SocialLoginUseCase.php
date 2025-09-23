<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Auth;

use Blog\Application\DTOs\Auth\AuthenticationResultDTO;
use Blog\Application\DTOs\Auth\SocialLoginDTO;
use Blog\Application\Services\Auth\JwtServiceInterface;
use Blog\Application\Services\Auth\SocialAuthServiceInterface;
use Blog\Domain\User\Entities\User;
use Blog\Domain\User\Exceptions\AuthenticationException;
use Blog\Domain\User\Repositories\UserRepositoryInterface;
use Blog\Domain\User\ValueObjects\Email;

final readonly class SocialLoginUseCase
{
    public function __construct(
        private SocialAuthServiceInterface $socialAuthService,
        private UserRepositoryInterface $userRepository,
        private JwtServiceInterface $jwtService
    ) {}

    public function execute(SocialLoginDTO $dto): AuthenticationResultDTO
    {
        // Get user data from social provider
        $socialUserData = $this->socialAuthService->handleProviderCallback($dto->provider);

        $email = new Email($socialUserData->getEmail());

        // Check if user already exists
        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser) {
            // Link social account to existing user if not already linked
            if (!$existingUser->hasSocialAccount()) {
                $existingUser->linkSocialAccount(
                    $socialUserData->getSocialId(),
                    $socialUserData->getProvider()
                );
                $this->userRepository->save($existingUser);
            }

            $user = $existingUser;
        } else {
            // Create new user from social data
            $user = User::createFromSocial($socialUserData);
            $this->userRepository->save($user);
        }

        // Validate user can authenticate
        if (!$user->isActive()) {
            throw AuthenticationException::userNotActive();
        }

        // Generate JWT token
        $token = $this->jwtService->generateToken($user);

        return new AuthenticationResultDTO(
            token: $token,
            user: $user,
            expiresAt: $this->jwtService->getTokenExpiration($token)
        );
    }

    public function getRedirectUrl(SocialLoginDTO $dto): string
    {
        return $this->socialAuthService->redirectToProvider($dto->provider);
    }
}