<?php

declare(strict_types=1);

namespace App\src\Application\DTOs\Auth;

use App\src\Domain\User\ValueObjects\SocialProvider;

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