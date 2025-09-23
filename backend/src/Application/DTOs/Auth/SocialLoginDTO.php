<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Auth;

use Blog\Domain\User\ValueObjects\SocialProvider;

final readonly class SocialLoginDTO
{
    public function __construct(
        public SocialProvider $provider
    ) {}

    public static function fromProvider(string $provider): self
    {
        return new self(
            provider: SocialProvider::fromString($provider)
        );
    }
}