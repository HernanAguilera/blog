<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Services;

use Blog\Application\Services\Auth\SocialAuthServiceInterface;
use Blog\Domain\User\ValueObjects\SocialProvider;
use Blog\Domain\User\ValueObjects\SocialUserData;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

final class SocialAuthService implements SocialAuthServiceInterface
{
    public function redirectToProvider(SocialProvider $provider): string
    {
        if (!$this->isProviderEnabled($provider)) {
            throw new \InvalidArgumentException("Social provider '{$provider->value}' is not enabled");
        }

        return Socialite::driver($provider->getDriverName())->redirect()->getTargetUrl();
    }

    public function handleProviderCallback(SocialProvider $provider): SocialUserData
    {
        if (!$this->isProviderEnabled($provider)) {
            throw new \InvalidArgumentException("Social provider '{$provider->value}' is not enabled");
        }

        try {
            $socialiteUser = Socialite::driver($provider->getDriverName())->user();

            $this->validateSocialiteUser($socialiteUser);

            return SocialUserData::fromSocialiteUser($socialiteUser, $provider);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                "Failed to authenticate with {$provider->getDisplayName()}: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function isProviderEnabled(SocialProvider $provider): bool
    {
        return $provider->isEnabled();
    }

    public function getEnabledProviders(): array
    {
        return SocialProvider::getEnabledProviders();
    }

    private function validateSocialiteUser(SocialiteUser $user): void
    {
        if (empty($user->getId())) {
            throw new \RuntimeException('Social provider did not return a user ID');
        }

        if (empty($user->getEmail())) {
            throw new \RuntimeException('Social provider did not return an email address');
        }

        if (empty($user->getName())) {
            throw new \RuntimeException('Social provider did not return a user name');
        }

        // Validate email format
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Social provider returned an invalid email address');
        }
    }
}