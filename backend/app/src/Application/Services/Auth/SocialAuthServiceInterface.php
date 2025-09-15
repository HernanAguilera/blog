<?php

declare(strict_types=1);

namespace App\src\Application\Services\Auth;

use App\src\Domain\User\ValueObjects\SocialProvider;
use App\src\Domain\User\ValueObjects\SocialUserData;

interface SocialAuthServiceInterface
{
    /**
     * Redirect user to social provider authentication page
     */
    public function redirectToProvider(SocialProvider $provider): string;

    /**
     * Handle callback from social provider and extract user data
     */
    public function handleProviderCallback(SocialProvider $provider): SocialUserData;

    /**
     * Check if a social provider is enabled
     */
    public function isProviderEnabled(SocialProvider $provider): bool;

    /**
     * Get all enabled social providers
     *
     * @return SocialProvider[]
     */
    public function getEnabledProviders(): array;
}