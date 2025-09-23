<?php

declare(strict_types=1);

namespace Blog\Application\Services\Security;

interface TurnstileServiceInterface
{
    /**
     * Verify Turnstile token from the request
     */
    public function verify(string $token, ?string $ip = null): bool;

    /**
     * Check if Turnstile is enabled
     */
    public function isEnabled(): bool;

    /**
     * Get the site key for frontend
     */
    public function getSiteKey(): ?string;
}