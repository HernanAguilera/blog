<?php

declare(strict_types=1);

namespace Blog\Domain\User\ValueObjects;

enum SocialProvider: string
{
    case GOOGLE = 'google';
    case FACEBOOK = 'facebook';
    case TWITTER = 'twitter';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::GOOGLE => 'Google',
            self::FACEBOOK => 'Facebook',
            self::TWITTER => 'Twitter (X)',
        };
    }

    public function getDriverName(): string
    {
        return $this->value;
    }

    public static function fromString(string $provider): self
    {
        return self::from(strtolower($provider));
    }

    public function isEnabled(): bool
    {
        return match ($this) {
            self::GOOGLE => !empty(config('services.google.client_id')),
            self::FACEBOOK => !empty(config('services.facebook.client_id')),
            self::TWITTER => !empty(config('services.twitter.client_id')),
        };
    }

    public static function getEnabledProviders(): array
    {
        return array_filter(
            self::cases(),
            fn(self $provider) => $provider->isEnabled()
        );
    }
}