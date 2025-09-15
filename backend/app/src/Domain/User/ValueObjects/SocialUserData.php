<?php

declare(strict_types=1);

namespace App\src\Domain\User\ValueObjects;

final readonly class SocialUserData
{
    public function __construct(
        public string $socialId,
        public SocialProvider $provider,
        public string $email,
        public string $name,
        public ?string $avatar = null,
        public ?string $nickname = null
    ) {
        if (empty($this->socialId)) {
            throw new \InvalidArgumentException('Social ID cannot be empty');
        }

        if (empty($this->email)) {
            throw new \InvalidArgumentException('Email cannot be empty');
        }

        if (empty($this->name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
    }

    public static function fromSocialiteUser(\Laravel\Socialite\Contracts\User $socialiteUser, SocialProvider $provider): self
    {
        return new self(
            socialId: (string) $socialiteUser->getId(),
            provider: $provider,
            email: $socialiteUser->getEmail(),
            name: $socialiteUser->getName(),
            avatar: $socialiteUser->getAvatar(),
            nickname: $socialiteUser->getNickname()
        );
    }

    public function getSocialId(): string
    {
        return $this->socialId;
    }

    public function getProvider(): SocialProvider
    {
        return $this->provider;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function toArray(): array
    {
        return [
            'social_id' => $this->socialId,
            'provider' => $this->provider->value,
            'email' => $this->email,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'nickname' => $this->nickname,
        ];
    }
}